<?php

namespace Creavo\MultiAppBundle\Controller;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Entity\Activity;
use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Entity\Item;
use Creavo\MultiAppBundle\Entity\Workspace;
use Creavo\MultiAppBundle\Form\Type\ActivityCommentType;
use Creavo\MultiAppBundle\Form\Type\ItemType;
use Creavo\MultiAppBundle\Helper\FilterHelper;
use Creavo\MultiAppBundle\Helper\Normalizer;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\IsTrue;

class ItemController extends Controller {

    /**
     * @Route("/{workspaceSlug}/{appSlug}", name="crv_ma_item_list")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listItemsAction(Workspace $workspace, App $app, Request $request) {

        /** @var FilterHelper $filterHelper */
        $filterHelper=$this->get('creavo_multi_app.helper.filter_helper');

        $filterTexts=$filterHelper->getFilterTexts($app,$request);

        return $this->render('CreavoMultiAppBundle:item:list.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appFields'=>$app->getAppFieldsFromApp(),
            'filterTexts'=>$filterTexts,
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/ajax", name="crv_ma_item_list_ajax")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listItemsAjaxAction(Workspace $workspace, App $app, Request $request) {

        $columns=$request->request->get('columns');
        $order=$request->request->get('order');
        $search=$request->request->get('search');

        $data=[
            'draw'=>$request->request->getInt('draw'),
            'recordsTotal'=>0,
            'recordsFiltered'=>0,
            'data'=>[],
            'filterTexts'=>'',
        ];

        $qb=$this->getDoctrine()->getRepository('CreavoMultiAppBundle:Item')->getQueryBuilderByApp($app);
        $qb
            ->addSelect('ir')
            ->join('i.currentRevision','ir');

        $data['recordsTotal']=(clone $qb)->select('COUNT(i)')->getQuery()->getSingleScalarResult();

        if(
            $search AND
            isset($search['value']) AND
            $searchTerm=$search['value']
        ) {
            $searchTerms=explode(' ',$searchTerm);
            foreach($searchTerms AS $number=>$term) {
                $qb
                    ->andWhere("JSON_SEARCH(ir.data,'one','%".$term."%') IS NOT NULL");
            }
        }

        /** @var FilterHelper $filterHelper */
        $filterHelper=$this->get('creavo_multi_app.helper.filter_helper');

        $filterTexts=$filterHelper->getFilterTexts($app,$request);
        $filterHelper->modifyQueryBuilder($app,$request,$qb);
        $data['filterTexts']=implode(', ',$filterTexts);

        $data['recordsFiltered']=(clone $qb)->select('COUNT(i)')->getQuery()->getSingleScalarResult();

        $qb
            ->setFirstResult($request->request->getInt('start',0))
            ->setMaxResults($request->request->getInt('length',10));

        if(
            isset($order[0]['column']) AND
            isset($order[0]['dir']) AND
            isset($columns[$order[0]['column']]) AND
            $dir=$order[0]['dir'] AND
            in_array($dir,['asc','desc'],false)
        ) {
            $slug=$columns[$order[0]['column']]['name'];
            $qb->addOrderBy("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$slug."'))",$dir);
        }

        $items=$qb->getQuery()->getResult();

        /** @var Item $item */
        foreach((array)$items AS $item) {

            /** @var AppField $appField */
            foreach($this->get('creavo_multi_app.helper.item_helper')->getItemRow($item) AS $appField) {
                $fields[$appField->getSlug()]=$this->get('creavo_multi_app.helper.format_helper')->renderAppFieldData($appField);
            }
            $fields['DT_RowId']='item_'.$item->getId();
            $fields['DT_RowData']=[
                'id'=>$item->getId(),
                'link'=>$this->generateUrl('crv_ma_item_detail',[
                    'workspaceSlug'=>$workspace->getSlug(),
                    'appSlug'=>$app->getSlug(),
                    'itemId'=>$item->getItemId(),
                ]),
            ];
            $data['data'][]=$fields;
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/create-item", name="crv_ma_item_create")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function itemCreateAction(Workspace $workspace, App $app, Request $request) {

        $form=$this->createForm(ItemType::class,[],[
            'appFields'=>$app->getAppFieldsFromApp(),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()) {
            $data=$form->getData();

            $item=$this->get('creavo_multi_app.helper.item_helper')->createItem($app,$data);

            $this->addFlash('success','Die Änderungen wurden gespeichert.');

            if($request->request->get('redirect')==='list') {
                return $this->redirectToRoute('crv_ma_item_list',[
                    'workspaceSlug'=>$workspace->getSlug(),
                    'appSlug'=>$app->getSlug(),
                ]);
            }
            if($request->request->get('redirect')==='create') {
                return $this->redirectToRoute('crv_ma_item_create',[
                    'workspaceSlug'=>$workspace->getSlug(),
                    'appSlug'=>$app->getSlug(),
                ]);
            }

            return $this->redirectToRoute('crv_ma_item_detail',[
                'workspaceSlug'=>$workspace->getSlug(),
                'appSlug'=>$app->getSlug(),
                'itemId'=>$item->getItemId(),
            ]);
        }

        return $this->render('@CreavoMultiApp/item/create.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/{itemId}/detail", name="crv_ma_item_detail")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @param Workspace $workspace
     * @param App $app
     * @param $itemId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function itemDetailAction(Workspace $workspace, App $app, $itemId, Request $request) {

        /** @var Item $item */
        $item=$this->getDoctrine()->getRepository('CreavoMultiAppBundle:Item')->getByAppAndItemId($app,$itemId);

        $itemRevision=$item->getCurrentRevision();
        if(
            $revisionNumber=$request->query->getInt('revision') AND
            $this->getDoctrine()->getRepository('CreavoMultiAppBundle:ItemRevision')->getByItemAndNumber($item,$revisionNumber)
        ) {
            $itemRevision=$this->getDoctrine()->getRepository('CreavoMultiAppBundle:ItemRevision')->getByItemAndNumber($item,$revisionNumber);
        }

        $form=$this->createForm(ActivityCommentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()) {
            $activity=new Activity($item,Activity::TYPE_COMMENT,$this->getUser());
            $activity->setComment($form['comment']->getData());
            $this->getDoctrine()->getManager()->persist($activity);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','Der Kommentar wurde gespeichert.');
            return $this->redirectToRoute('crv_ma_item_detail',[
                'workspaceSlug'=>$workspace->getSlug(),
                'appSlug'=>$app->getSlug(),
                'itemId'=>$item->getItemId(),
            ]);
        }

        return $this->render('@CreavoMultiApp/item/detail.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appFields'=>$this->get('creavo_multi_app.helper.item_helper')->getItemRow($item,$itemRevision),
            'item'=>$item,
            'itemRevision'=>$itemRevision,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/{itemId}/edit", name="crv_ma_item_edit")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @param Workspace $workspace
     * @param App $app
     * @param $itemId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function itemEditAction(Workspace $workspace, App $app, $itemId, Request $request) {

        /** @var Item $item */
        $item=$this->getDoctrine()->getRepository('CreavoMultiAppBundle:Item')->getByAppAndItemId($app,$itemId);
        $itemRevision=$item->getCurrentRevision();

        if($item->isDeleted()) {
            throw $this->createNotFoundException('item is deleted');
        }

        $normalizer=new Normalizer($item->getApp()->getAppFieldsFromApp());
        $data=$normalizer->transformDataToPhp($itemRevision->getData());

        $form=$this->createForm(ItemType::class,$data,[
            'appFields'=>$app->getAppFieldsFromApp(),
            'em'=>$this->getDoctrine(),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()) {
            $data=$form->getData();

            $this->get('creavo_multi_app.helper.item_helper')->updateItem($item,$data);

            $this->addFlash('success','Die Änderungen wurden gespeichert.');

            if($request->request->get('redirect')==='list') {
                return $this->redirectToRoute('crv_ma_item_list',[
                    'workspaceSlug'=>$workspace->getSlug(),
                    'appSlug'=>$app->getSlug(),
                ]);
            }
            if($request->request->get('redirect')==='create') {
                return $this->redirectToRoute('crv_ma_item_create',[
                    'workspaceSlug'=>$workspace->getSlug(),
                    'appSlug'=>$app->getSlug(),
                ]);
            }

            return $this->redirectToRoute('crv_ma_item_detail',[
                'workspaceSlug'=>$workspace->getSlug(),
                'appSlug'=>$app->getSlug(),
                'itemId'=>$itemId,
            ]);
        }

        return $this->render('@CreavoMultiApp/item/edit.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appFields'=>$this->get('creavo_multi_app.helper.item_helper')->getItemRow($item,$itemRevision),
            'item'=>$item,
            'itemRevision'=>$itemRevision,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/{itemId}/delete", name="crv_ma_item_delete")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @param Workspace $workspace
     * @param App $app
     * @param $itemId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function itemDeleteAction(Workspace $workspace, App $app, $itemId, Request $request) {

        /** @var Item $item */
        $item=$this->getDoctrine()->getRepository('CreavoMultiAppBundle:Item')->getByAppAndItemId($app,$itemId);
        $itemRevision=$item->getCurrentRevision();

        if($item->isDeleted()) {
            throw $this->createNotFoundException('item is already deleted');
        }

        $builder=$this->createFormBuilder();
        $builder->add('delete',CheckboxType::class,[
            'label'=>$app->getItemSingularName().' wirklich löschen',
            'required'=>true,
            'constraints'=>[
                new IsTrue(),
            ]
        ]);

        $form=$builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()) {

            $this->get('creavo_multi_app.helper.item_helper')->softDeleteItem($item,$this->getUser());

            $this->addFlash('success',$app->getItemSingularName().' wurde gelöscht.');
            return $this->redirectToRoute('crv_ma_item_list',[
                'workspaceSlug'=>$workspace->getSlug(),
                'appSlug'=>$app->getSlug(),
            ]);
        }

        return $this->render('@CreavoMultiApp/item/delete.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appFields'=>$this->get('creavo_multi_app.helper.item_helper')->getItemRow($item,$itemRevision),
            'item'=>$item,
            'itemRevision'=>$itemRevision,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/{itemId}/restore", name="crv_ma_item_restore")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @param Workspace $workspace
     * @param App $app
     * @param $itemId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function itemRestoreAction(Workspace $workspace, App $app, $itemId, Request $request) {

        /** @var Item $item */
        $item=$this->getDoctrine()->getRepository('CreavoMultiAppBundle:Item')->getByAppAndItemId($app,$itemId);
        $itemRevision=$item->getCurrentRevision();

        if(!$item->isDeleted()) {
            throw $this->createNotFoundException('item is not soft-deleted');
        }

        $builder=$this->createFormBuilder();
        $builder->add('restore',CheckboxType::class,[
            'label'=>$app->getItemSingularName().' wirklich wiederherstellen',
            'required'=>true,
            'constraints'=>[
                new IsTrue(),
            ]
        ]);

        $form=$builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()) {

            $this->get('creavo_multi_app.helper.item_helper')->restoreItem($item,$this->getUser());

            $this->addFlash('success',$app->getItemSingularName().' wurde wiederhergestellt.');
            return $this->redirectToRoute('crv_ma_item_detail',[
                'workspaceSlug'=>$workspace->getSlug(),
                'appSlug'=>$app->getSlug(),
                'itemId'=>$item->getItemId(),
            ]);
        }

        return $this->render('@CreavoMultiApp/item/restore.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appFields'=>$this->get('creavo_multi_app.helper.item_helper')->getItemRow($item,$itemRevision),
            'item'=>$item,
            'itemRevision'=>$itemRevision,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/{itemId}/activity-ajax", name="crv_ma_item_activity_ajax")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @param Workspace $workspace
     * @param App $app
     * @param $itemId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function itemActivitiesAction(Workspace $workspace, App $app, $itemId, Request $request) {

        $data=[
            'items'=>[],
            'more'=>false,
        ];

        /** @var Item $item */
        $item=$this->getDoctrine()->getRepository('CreavoMultiAppBundle:Item')->getByAppAndItemId($app,$itemId);
        $offset=$request->query->getInt('offset',0);

        $qb=$this->getDoctrine()->getRepository('CreavoMultiAppBundle:Activity')->createQueryBuilder('a');

        $qb
            ->andWhere('a.item = :item')
            ->setParameter('item',$item)
            ->setFirstResult($offset)
            ->setMaxResults(11)
            ->addOrderBy('a.id','desc');

        if($type=$request->query->get('type')) {
            if($type==='activity') {
                $qb
                    ->andWhere('a.type != :type')
                    ->setParameter('type',Activity::TYPE_COMMENT);
            }elseif($type==='comments') {
                $qb
                    ->andWhere('a.type = :type')
                    ->setParameter('type',Activity::TYPE_COMMENT);
            }
        }

        $counter=0;

        /** @var Activity $activity */
        foreach($qb->getQuery()->getResult() AS $activity) {
            $counter++;

            if($counter<=10) {
                $createdBy=$this->get('creavo_multi_app.helper.item_helper')->getUserById($activity->getCreatedBy());
                $data['items'][]=[
                    'id'=>$activity->getId(),
                    'type'=>$activity->getType(),
                    'createdAt'=>$activity->getCreatedAt()->format('d.m.Y H:i:s'),
                    'createdBy'=>$createdBy ? $createdBy->__toString() : 'anonym',
                    'message'=>$activity->__toString(),
                    'comment'=>$activity->getComment(),
                    'hasDetail'=>$activity->hasDetail(),
                    'detailUrl'=>$this->generateUrl('crv_ma_item_activity_detail',[
                        'workspaceSlug'=>$workspace->getSlug(),
                        'appSlug'=>$app->getSlug(),
                        'itemId'=>$itemId,
                        'activityId'=>$activity->getId(),
                    ])
                ];
                continue;
            }

            $data['more']=true;
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/{itemId}/activity/{activityId}/detail", name="crv_ma_item_activity_detail")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug", "workspace": "workspace"}})
     * @ParamConverter("activity", options={"mapping": {"activityId": "id"}})
     * @param Workspace $workspace
     * @param App $app
     * @param $itemId
     * @param Activity $activity
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function itemActivityDetailAction(Workspace $workspace, App $app, $itemId, Activity $activity) {

        /** @var ObjectManager $em */
        $em=$this->getDoctrine()->getManager();

        /** @var Item $item */
        $item=$em->getRepository('CreavoMultiAppBundle:Item')->getByAppAndItemId($app,$itemId);

        if($activity->getItem()!==$item) {
            throw $this->createNotFoundException('activity does not match item');
        }

        $currentItemRevision=$activity->getItemRevision();
        $lastItemRevision=$em->getRepository('CreavoMultiAppBundle:ItemRevision')->getPreviousRevision($currentItemRevision);

        $changes=$this->get('creavo_multi_app.helper.item_helper')->generateDiffFromItemRevisions($lastItemRevision,$currentItemRevision);

        return $this->render('@CreavoMultiApp/activity/detail.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'activity'=>$activity,
            'item'=>$item,
            'previousActivity'=>$em->getRepository('CreavoMultiAppBundle:Activity')->getPreviousActivity($activity,Activity::TYPE_ITEM_CHANGES),
            'nextActivity'=>$em->getRepository('CreavoMultiAppBundle:Activity')->getNextActivity($activity,Activity::TYPE_ITEM_CHANGES),
            'currentItemRevision'=>$currentItemRevision,
            'lastItemRevision'=>$lastItemRevision,
            'changes'=>$changes,
        ]);
    }

}