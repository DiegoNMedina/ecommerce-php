<?php

namespace Controllers;

use Core\Controller;
use Models\Product;
use Models\Category;

class ProductController extends Controller
{
    /** @var Product */
    private $productModel;
    /** @var Category */
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $page = (int) ($this->getQuery('page', 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->findAll(
            [],
            ['created_at' => 'DESC'],
            $limit,
            $offset
        );

        $totalProducts = $this->productModel->count();
        $totalPages = ceil($totalProducts / $limit);

        $this->render('products/index', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'categories' => $this->categoryModel->getCategoryTree()
        ]);
    }

    public function show(int $id): void
    {
        $product = $this->productModel->findWithCategories($id);

        if (!$product) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        // Incrementar visitas
        $this->productModel->incrementVisits($id);

        // Calcular mensualidades
        $installments = [
            6 => $this->productModel->calculateInstallments($product['price'], 6),
            12 => $this->productModel->calculateInstallments($product['price'], 12)
        ];

        $this->render('products/show', [
            'product' => $product,
            'installments' => $installments,
            'comments' => $this->productModel->getComments($id),
            'relatedProducts' => $this->productModel->getRandomProducts(4)
        ]);
    }

    public function search(): void
    {
        $query = $this->getQuery('q', '');
        $page = (int) ($this->getQuery('page', 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        if (empty($query)) {
            $this->redirect('/');
            return;
        }

        $products = $this->productModel->search($query, $limit, $offset);
        $totalProducts = count($this->productModel->search($query));
        $totalPages = ceil($totalProducts / $limit);

        if ($this->isAjax()) {
            $this->json([
                'products' => $products,
                'totalPages' => $totalPages
            ]);
            return;
        }

        $this->render('products/search', [
            'products' => $products,
            'query' => $query,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function category(int $id): void
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $page = (int) ($this->getQuery('page', 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->findByCategory($id, $limit, $offset);
        $totalProducts = $this->categoryModel->getProductCount($id);
        $totalPages = ceil($totalProducts / $limit);

        $this->render('products/category', [
            'category' => $category,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'breadcrumb' => $this->categoryModel->getCategoryPath($id),
            'subcategories' => $this->categoryModel->getSubcategories($id),
            'accessories' => $this->categoryModel->getAccessories($id)
        ]);
    }

    public function addComment(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect("/product/{$id}");
            return;
        }

        $name = $this->getPost('name');
        $comment = $this->getPost('comment');
        $rating = (int) $this->getPost('rating');

        if (empty($name) || empty($comment) || $rating < 1 || $rating > 5) {
            $this->json([
                'success' => false,
                'message' => 'Datos inválidos'
            ], 400);
            return;
        }

        $commentId = $this->productModel->addComment($id, $name, $comment, $rating);

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'commentId' => $commentId
            ]);
            return;
        }

        $this->redirect("/product/{$id}#comment-{$commentId}");
    }

    public function featured(): void
    {
        $featuredProducts = $this->productModel->findAll(
            [],
            ['visits' => 'DESC'],
            10
        );

        // Calcular mensualidades para productos destacados
        foreach ($featuredProducts as &$product) {
            $product['monthly_payment'] = $this->productModel->calculateInstallments($product['price'], 12);
            $product['categories'] = []; // Inicializar como array vacío
            $product['rating'] = $product['avg_rating'] ?? 0;
        }

        // Obtener categorías para las pestañas
        $categories = $this->categoryModel->findAll();

        // Obtener productos más visitados por categoría
        $most_visited_products = [];
        foreach ($categories as $category) {
            $categoryProducts = $this->productModel->findByCategory(
                $category['id'],
                4, // límite de 4 productos por categoría
                0   // offset 0
            );

            // Calcular mensualidades para productos de categoría
            foreach ($categoryProducts as &$product) {
                $product['monthly_payment'] = $this->productModel->calculateInstallments($product['price'], 12);
            }

            $most_visited_products[$category['id']] = $categoryProducts;
        }

        if ($this->isAjax()) {
            $this->json(['products' => $featuredProducts]);
            return;
        }

        $this->render('products/featured', [
            'featured_products' => $featuredProducts,
            'categories' => $categories,
            'most_visited_products' => $most_visited_products
        ]);
    }
}
