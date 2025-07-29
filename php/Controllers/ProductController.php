<?php

namespace Controllers;

use Core\Controller;
use Models\Product;
use Models\Category;

class ProductController extends Controller {
    /** @var Product */
    private $productModel;
    /** @var Category */
    private $categoryModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index(): void {
        $page = (int) ($this->getQuery('page', 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->findAllWithRating($limit, $offset);

        $totalProducts = $this->productModel->count();
        $totalPages = ceil($totalProducts / $limit);

        $this->render('products/index', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'categories' => $this->categoryModel->getCategoryTree()
        ]);
    }

    public function show(int $id): void {
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

    public function search(): void {
        $query = $this->getQuery('q', '');
        $page = (int) ($this->getQuery('page', 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        // Obtener filtros
        $selectedCategories = $this->getQuery('categories', []);
        $selectedBrands = $this->getQuery('brands', []);
        $minPrice = $this->getQuery('min_price', '');
        $maxPrice = $this->getQuery('max_price', '');
        $sort = $this->getQuery('sort', 'relevance');

        if (empty($query)) {
            $this->redirect('/');
            return;
        }

        // Aplicar filtros en la búsqueda
        $filters = [
            'categories' => $selectedCategories,
            'brands' => $selectedBrands,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort' => $sort
        ];
        
        $products = $this->productModel->searchWithFilters($query, $filters, $limit, $offset);
        $totalProducts = count($this->productModel->searchWithFilters($query, $filters));
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
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'brands' => $this->productModel->getAllBrands(),
            'categories' => $this->categoryModel->findAll(),
            'selectedCategories' => $selectedCategories,
            'selectedBrands' => $selectedBrands,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sort' => $sort
        ]);
    }

    public function category(int $id): void {
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

        // Obtener productos destacados filtrados por categoría
        $featuredProducts = $this->productModel->getFeaturedByCategory($id, 10);
        
        // Obtener productos más vendidos filtrados por categoría
        $bestSellingProducts = $this->productModel->getBestSellingByCategory($id, 10);

        // Agregar campos faltantes a los productos
        foreach ($products as &$product) {
            $product['name'] = $product['model']; // Usar model como name
            $product['monthly_payment'] = $this->productModel->calculateInstallments($product['price'], 12);
            $product['rating'] = $product['avg_rating'] ?? 0; // Usar avg_rating como rating
        }
        
        // Calcular cuotas para productos destacados
        foreach ($featuredProducts as &$product) {
            $product['installments'] = $this->productModel->calculateInstallments($product['price'], 12);
        }
        
        // Calcular cuotas para productos más vendidos
        foreach ($bestSellingProducts as &$product) {
            $product['installments'] = $this->productModel->calculateInstallments($product['price'], 12);
        }

        $this->render('products/category', [
            'category' => $category,
            'products' => $products,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'breadcrumbs' => $this->categoryModel->getCategoryPath($id),
            'subcategories' => $this->categoryModel->getSubcategories($id),
            'accessories' => $this->categoryModel->getAccessories($id),
            'featuredProducts' => $featuredProducts,
            'bestSellingProducts' => $bestSellingProducts
        ]);
    }

    public function addComment(int $id): void {
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

    public function like(int $id): void {
        if (!$this->isPost()) {
            http_response_code(405);
            $this->json([
                'success' => false,
                'message' => 'Método no permitido'
            ], 405);
            return;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            http_response_code(404);
            $this->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
            return;
        }

        $success = $this->productModel->incrementLikes($id);
        
        if ($success) {
            $newLikes = $this->productModel->getLikes($id);
            $this->json([
                'success' => true,
                'likes' => $newLikes,
                'message' => '¡Gracias por tu like!'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Error al procesar el like'
            ], 500);
        }
    }

    public function featured(): void {
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

    public function bestSelling(): void {
        $page = (int) ($this->getQuery('page', 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getBestSelling($limit, $offset);
        $totalProducts = $this->productModel->getTotalBestSelling();
        $totalPages = ceil($totalProducts / $limit);

        // Calcular mensualidades para cada producto
        foreach ($products as &$product) {
            $product['monthly_payment'] = $this->productModel->calculateInstallments($product['price'], 12);
        }

        if ($this->isAjax()) {
            $this->json([
                'products' => $products,
                'totalPages' => $totalPages
            ]);
            return;
        }

        $this->render('products/best-selling', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'categories' => $this->categoryModel->getCategoryTree()
        ]);
    }
}