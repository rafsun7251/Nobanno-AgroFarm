<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../Model/Crop.php';


class CropController extends BaseController
{
    private Crop $cropModel;


    public function __construct()
    {
        $this->cropModel =
            new Crop();
    }


    // ==========================================
    // PUBLIC CROP LIST
    // ==========================================

    public function index(): void
    {
        $crops =
            $this->cropModel
                ->getActive();


        $this->view(
            'home/crops',
            [
                'crops' =>
                    $crops
            ]
        );
    }


    // ==========================================
    // SEARCH
    // ==========================================

    public function search(): void
    {
        $keyword =
            trim(
                $_GET['search'] ?? ''
            );

        $category =
            trim(
                $_GET['category'] ?? ''
            );


        $crops =
            $this->cropModel
                ->search(
                    $keyword,
                    $category
                );


        $this->view(
            'home/crops',
            [
                'crops' =>
                    $crops,

                'keyword' =>
                    $keyword,

                'category' =>
                    $category
            ]
        );
    }


    // ==========================================
    // DETAILS
    // ==========================================

    public function details(): void
    {
        $cropId =
            (int)(
                $_GET['id'] ?? 0
            );


        if ($cropId <= 0) {

            $this->redirect(
                'index.php?page=crops'
            );
        }


        $crop =
            $this->cropModel
                ->findById(
                    $cropId
                );


        if (!$crop) {

            $this->setMessage(
                'error',
                'Crop not found.'
            );

            $this->redirect(
                'index.php?page=crops'
            );
        }


        $this->view(
            'home/product_details',
            [
                'crop' =>
                    $crop
            ]
        );
    }
}