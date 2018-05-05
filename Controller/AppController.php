<?php

namespace Creavo\MultiAppBundle\Controller;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Entity\Workspace;
use Creavo\MultiAppBundle\Form\Type\AppBasicType;
use Creavo\MultiAppBundle\Form\Type\AppFieldType;
use Creavo\MultiAppBundle\Helper\FilterHelper;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

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
     * @Route("/{workspaceSlug}/create-app", name="crv_ma_app_create")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @param Workspace $workspace
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAppAction(Workspace $workspace, Request $request) {
        $app=new App();
        $app->setWorkspace($workspace);
        return $this->editAppBasicsAction($workspace,$app,$request);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/edit-basics", name="crv_ma_app_edit_basics")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAppBasicsAction(Workspace $workspace, App $app, Request $request) {

        $form=$this->createForm(AppBasicType::class,$app);
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()) {

            if(!$app->getSlug()) {
                $app->setSlug($this->get('creavo_multi_app.helper.slug_helper')->createSlugForApp($app));
            }

            $this->getDoctrine()->getManager()->persist($app);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Die App wurde gespeichert.');

            return $this->redirectToRoute('crv_ma_app_edit_basics',[
                'workspaceSlug'=>$workspace->getSlug(),
                'appSlug'=>$app->getSlug(),
            ]);
        }

        return $this->render('@CreavoMultiApp/app/edit_basics.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/edit-fields", name="crv_ma_app_edit_fields")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAppFieldsAction(Workspace $workspace, App $app, Request $request) {

        if($action=$request->query->get('action')) {

            /** @var ObjectManager $em */
            $em=$this->getDoctrine()->getManager();

            switch($action) {
                case 'clear':
                    $request->getSession()->remove('crv_ma_field_data');
                    $this->addFlash('success','Die Felder wurden zurückgesetzt.');
                    break;

                case 'save':
                    $fields=$this->getSessionData($app,$request);
                    if(count($fields)>0) {
                        $app->setAppFieldsForApp($fields);
                        $request->getSession()->remove('crv_ma_field_data');
                        $this->addFlash('success','Die Änderungen wurden gespeichert.');
                        $em->flush();
                    }else{
                        $this->addFlash('warning','Speicherung fehlgeschlagen - es muss mindestens ein Feld geben.');
                    }
            }

            return $this->redirectToRoute('crv_ma_app_edit_fields',[
                'workspaceSlug'=>$workspace->getSlug(),
                'appSlug'=>$app->getSlug(),
            ]);
        }

        $fields=$this->getSessionData($app,$request);

        return $this->render('@CreavoMultiApp/app/edit_fields.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'fields'=>$fields,
        ]);
    }

    protected function setSessionData(App $app, Request $request, array $data) {
        /** @var SessionInterface $session */
        $session=$request->getSession();

        $crvMaFieldData=$session->get('crv_ma_field_data');

        $crvMaFieldData[$app->getId()]=$data;
        $session->set('crv_ma_field_data',$crvMaFieldData);
        return $data;
    }

    protected function getSessionData(App $app, Request $request) {
        /** @var SessionInterface $session */
        $session=$request->getSession();

        $crvMaFieldData=$session->get('crv_ma_field_data');

        if(!isset($crvMaFieldData[$app->getId()])) {
            $this->setSessionData($app,$request,$app->getAppFieldsFromApp());
            return $this->getSessionData($app,$request);
        }

        return $crvMaFieldData[$app->getId()];
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/edit-field-modal", name="crv_ma_app_edit_field_modal")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAppFieldModalAction(Workspace $workspace, App $app, Request $request) {

        $fieldNumber=$request->query->getInt('field',-1);
        $fields=$this->getSessionData($app,$request);

        if(!isset($fields[$fieldNumber])) {
            throw $this->createNotFoundException('field with number '.$fieldNumber.' not found');
        }

        /** @var AppField $appField */
        $appField=$fields[$fieldNumber];

        $form=$this->createForm(AppFieldType::class,$appField,[
            'em'=>$this->getDoctrine(),
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() AND $form->isValid()) {

            $fields[$fieldNumber]=$appField;
            $this->setSessionData($app,$request,$fields);

            return $this->render('@CreavoMultiApp/partials/modal_close.html.twig');
        }

        return $this->render('@CreavoMultiApp/app/edit_field_modal.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appField'=>$appField,
            'form'=>$form->createView(),
            'fieldNumber'=>$fieldNumber,
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/remove-field-modal", name="crv_ma_app_remove_field_modal")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAppFieldModalAction(Workspace $workspace, App $app, Request $request) {

        $fieldNumber=$request->query->getInt('field',-1);
        $fields=$this->getSessionData($app,$request);

        if(!isset($fields[$fieldNumber])) {
            throw $this->createNotFoundException('field with number '.$fieldNumber.' not found');
        }

        /** @var AppField $appField */
        $appField=$fields[$fieldNumber];

        $builder=$this->createFormBuilder();
        $builder->add('check',CheckboxType::class,[
            'label'=>'Das Feld wirklich endgültig entfernen?',
            'required'=>true,
            'constraints'=>[
                new IsTrue(),
            ],
        ]);

        $form=$builder->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() AND $form->isValid()) {

            unset($fields[$fieldNumber]);
            $this->setSessionData($app,$request,$fields);

            return $this->render('@CreavoMultiApp/partials/modal_close.html.twig');
        }

        return $this->render('@CreavoMultiApp/app/remove_field_modal.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'appField'=>$appField,
            'form'=>$form->createView(),
            'fieldNumber'=>$fieldNumber,
        ]);
    }

    /**
     * @Route("/{workspaceSlug}/{appSlug}/modal-filters", name="crv_ma_app_modal_filters")
     * @ParamConverter("workspace", options={"mapping": {"workspaceSlug": "slug"}})
     * @ParamConverter("app", options={"mapping": {"appSlug": "slug"}})
     * @param Workspace $workspace
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function modalFilterAction(Workspace $workspace, App $app, Request $request) {

        /** @var FilterHelper $filterHelper */
        $filterHelper=$this->get('creavo_multi_app.helper.filter_helper');

        if($request->query->getInt('removeFilter',-1)>=0) {
            $removeFilter=$request->query->getInt('removeFilter',-1);
            $filterHelper->removeFilter($app,$request,$removeFilter);
        }elseif($request->query->get('action')==='removeAll') {
            $filterHelper->removeAllFilters($app,$request);
        }

        $formViews=[];
        $possibleFilters=$filterHelper->getPossibleFilters($app);

        foreach($possibleFilters AS $slug=>$possibleFilter) {

            /**
             * @var int $key
             * @var FilterInterface $filterEntity
             */
            foreach((array)$possibleFilter['filters'] AS $key=>$filterEntity) {

                $builder=$this->get('form.factory')->createNamedBuilder($slug.'_'.$key);
                if($filterEntity->getValue1FormType()) {
                    $builder->add('value1',$filterEntity->getValue1FormType(),$filterEntity->getValue1FormOptions());
                }

                if($filterEntity->getValue2FormType()) {
                    $builder->add('value2',$filterEntity->getValue2FormType(),$filterEntity->getValue2FormOptions());
                }

                $form=$builder->getForm();
                $form->handleRequest($request);

                if($form->isSubmitted() AND $form->isValid()) {
                    $filterEntity->setValue1($form['value1']->getData());

                    if(isset($form['value2'])) {
                        $filterEntity->setValue2($form['value2']->getData());
                    }

                    $filterHelper->addFilter($app,$request,$filterEntity);
                    return $this->redirectToRoute('crv_ma_app_modal_filters',[
                        'workspaceSlug'=>$workspace->getSlug(),
                        'appSlug'=>$app->getSlug(),
                    ]);
                }

                $formViews[$slug][$key]=[
                    'form'=>$form->createView(),
                    'filter'=>$filterEntity,
                ];
            }
        }

        return $this->render('@CreavoMultiApp/app/modal_filters.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
            'filterObjects'=>$filterHelper->getFilterObjects($app,$request),
            'possibleFilters'=>$possibleFilters,
            'formViews'=>$formViews,
        ]);
    }

}