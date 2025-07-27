<?php

namespace Controllers;

use Core\Controller;
use Models\Product;
use Models\Category;

class HomeController extends Controller {
    /** @var Product */
    private $productModel;
    /** @var Category */
    private $categoryModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index(): void {
        // Obtener categorías principales con sus subcategorías
        $categories = $this->categoryModel->getCategoryTree();

        // Obtener productos destacados aleatorios
        $featuredProducts = $this->productModel->getRandomProducts(10);

        // Obtener productos más visitados por categoría
        $mostVisitedByCategory = [];
        foreach ($categories as $category) {
            $mostVisitedByCategory[$category['name']] = $this->productModel->findByCategory(
                $category['id'],
                10,
                0
            );
        }

        // Calcular mensualidades para productos destacados
        foreach ($featuredProducts as &$product) {
            $product['installments'] = [
                6 => $this->productModel->calculateInstallments($product['price'], 6),
                12 => $this->productModel->calculateInstallments($product['price'], 12)
            ];
        }

        $this->render('home/index', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'mostVisitedByCategory' => $mostVisitedByCategory
        ]);
    }

    public function about(): void {
        $this->render('home/about');
    }

    public function contact(): void {
        if ($this->isPost()) {
            // Procesar el formulario de contacto
            $name = $this->getPost('name');
            $email = $this->getPost('email');
            $message = $this->getPost('message');

            // Aquí se podría agregar la lógica para enviar el email
            // Por ahora solo simulamos una respuesta exitosa

            if ($this->isAjax()) {
                $this->json([
                    'success' => true,
                    'message' => 'Mensaje enviado correctamente'
                ]);
                return;
            }

            $this->redirect('/contact?success=1');
            return;
        }

        $this->render('home/contact');
    }

    public function installmentCalculator(): void {
        if ($this->isPost()) {
            $price = (float) $this->getPost('price', 0);
            $months = (int) $this->getPost('months', 6);

            if ($price <= 0 || !in_array($months, [6, 12])) {
                $this->json([
                    'success' => false,
                    'message' => 'Datos inválidos'
                ], 400);
                return;
            }

            $monthlyPayment = $this->productModel->calculateInstallments($price, $months);

            $this->json([
                'success' => true,
                'monthlyPayment' => $monthlyPayment,
                'totalAmount' => $monthlyPayment * $months
            ]);
            return;
        }

        $this->render('home/calculator');
    }
}