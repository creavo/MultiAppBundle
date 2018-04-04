<?php

namespace Creavo\MultiAppBundle\Controller;

use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Entity\Item;
use Creavo\MultiAppBundle\Entity\Workspace;
use Creavo\MultiAppBundle\Form\Type\ItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppController
 * @package Creavo\MultiAppBundle\Controller
 */
class AppController extends Controller {

    /**
     * @Route("/", name="crv_ma_workspace_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listWorkspacesAction(Request $request) {
        return $this->render('CreavoMultiAppBundle:workspace:list.html.twig',[
            'pagination'=>$this->get('knp_paginator')->paginate($this->getDoctrine()->getRepository('CreavoMultiAppBundle:Workspace')->createQueryBuilder('w'),$request->query->getInt('page',1),25),
        ]);
    }

    /**
     * @Route("/{workspaceSlug}", name="crv_ma_app_list")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @param Workspace $workspace
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAppsAction(Workspace $workspace, Request $request) {
        return $this->render('CreavoMultiAppBundle:app:list.html.twig',[
            'workspace'=>$workspace,
            'pagination'=>$this->get('knp_paginator')->paginate($this->getDoctrine()->getRepository('CreavoMultiAppBundle:App')->getByWorkspace($workspace),$request->query->getInt('page',1),25),
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}", name="crv_ma_item_list")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listItemsAction(Workspace $workspace, App $app, Request $request) {

        $pagination=$this->get('knp_paginator')->paginate($this->getDoctrine()->getRepository('CreavoMultiAppBundle:Item')->getQueryBuilderByApp($app),$request->query->getInt('page',1),25);

        $items=[];
        foreach($pagination AS $item) {
            $items[]=[
                'item'=>$item,
                'fields'=>$this->get('creavo_multi_app.helper.item_helper')->getItemRow($item),
            ];
        }
        $pagination->setItems($items);

        return $this->render('CreavoMultiAppBundle:item:list.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appFields'=>$this->get('creavo_multi_app.helper.item_helper')->getAppFieldsFromApp($app),
            'pagination'=>$pagination,
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/{itemId}/detail", name="crv_ma_item_detail")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug"}})
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

        return $this->render('@CreavoMultiApp/item/detail.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appFields'=>$this->get('creavo_multi_app.helper.item_helper')->getItemRow($item,$itemRevision),
            'item'=>$item,
            'itemRevision'=>$itemRevision,
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/{itemId}/edit", name="crv_ma_item_edit")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug"}})
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

        $form=$this->createForm(ItemType::class,$itemRevision->getData(),[
            'appFields'=>$this->get('creavo_multi_app.helper.item_helper')->getAppFieldsFromApp($app),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()) {
            $data=$form->getData();

            $this->get('creavo_multi_app.helper.item_helper')->updateItem($item,$data);

            $this->addFlash('success','Die Änderungen wurden gespeichert.');
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

}