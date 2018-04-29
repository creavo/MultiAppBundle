<?php

namespace Creavo\MultiAppBundle\Controller;

use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Entity\Workspace;
use Creavo\MultiAppBundle\Form\Type\AppBasicType;
use Creavo\MultiAppBundle\Helper\FilterHelper;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        return $this->render('@CreavoMultiApp/app/edit_fields.html.twig',[
            'workspace'=>$workspace,
            'appEntity'=>$app,
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

            $builder=$this->get('form.factory')->createNamedBuilder($slug);
            $builder
                ->add('choice_filter',ChoiceType::class,[
                    'label'=>'Filter',
                    'choice_label'=>function(FilterInterface $filter) {
                        return $filter->toText();
                    },
                    'choices'=>$possibleFilter['filters'],
                    'required'=>true,
                ])
                ->add('value1',TextType::class,[
                    'label'=>'Wert',
                    'required'=>true,
                ]);

            $form=$builder->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() AND $form->isValid()) {
                /** @var FilterInterface $filter */
                $filter=$form['choice_filter']->getData();
                $filter->setValue1($form['value1']->getData());

                $filterHelper->addFilter($app,$request,$filter);
                return $this->redirectToRoute('crv_ma_app_modal_filters',[
                    'workspaceSlug'=>$workspace->getSlug(),
                    'appSlug'=>$app->getSlug(),
                ]);
            }

            $formViews[$slug]=$form->createView();
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