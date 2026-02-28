<?php

namespace Modules\SupportAgent\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Model Context Protocol (MCP) Service
 * 
 * This service provides the LLM with context about the codebase structure,
 * file relationships, and actual code implementations to give accurate
 * guidance about how features work in the system.
 */
class MCPService
{
    protected $basePath;
    protected $contextDepth;
    protected $enabled;
    
    // File patterns to analyze
    protected $patterns = [
        'controllers' => 'app/Http/Controllers/*.php',
        'models' => 'app/*.php',
        'routes' => 'routes/*.php',
        'views' => 'resources/views/**/*.blade.php',
        'modules' => 'Modules/*/Http/Controllers/*.php',
        'services' => 'app/Services/*.php',
        'utils' => 'app/Utils/*.php',
    ];
    
    // Feature to file mapping for quick lookups
    protected $featureMap = [
        'pos' => [
            'controller' => 'app/Http/Controllers/SellPosController.php',
            'routes' => 'routes/web.php',
            'views' => 'resources/views/sale_pos/',
        ],
        'sale' => [
            'controller' => 'app/Http/Controllers/SellController.php',
            'model' => 'app/Transaction.php',
            'routes' => 'routes/web.php',
        ],
        'product' => [
            'controller' => 'app/Http/Controllers/ProductController.php',
            'model' => 'app/Product.php',
            'views' => 'resources/views/product/',
        ],
        'inventory' => [
            'controller' => 'app/Http/Controllers/StockAdjustmentController.php',
            'model' => 'app/StockAdjustmentLine.php',
        ],
        'stock' => [
            'controller' => 'app/Http/Controllers/StockTransferController.php',
            'utils' => 'app/Utils/ProductUtil.php',
        ],
        'contact' => [
            'controller' => 'app/Http/Controllers/ContactController.php',
            'model' => 'app/Contact.php',
            'views' => 'resources/views/contact/',
        ],
        'customer' => [
            'controller' => 'app/Http/Controllers/ContactController.php',
            'model' => 'app/Contact.php',
        ],
        'supplier' => [
            'controller' => 'app/Http/Controllers/ContactController.php',
            'model' => 'app/Contact.php',
        ],
        'purchase' => [
            'controller' => 'app/Http/Controllers/PurchaseController.php',
            'model' => 'app/Transaction.php',
        ],
        'report' => [
            'controller' => 'app/Http/Controllers/ReportController.php',
            'views' => 'resources/views/report/',
        ],
        'user' => [
            'controller' => 'app/Http/Controllers/ManageUserController.php',
            'model' => 'app/User.php',
        ],
        'role' => [
            'controller' => 'app/Http/Controllers/RoleController.php',
        ],
        'tax' => [
            'controller' => 'app/Http/Controllers/TaxRateController.php',
            'model' => 'app/TaxRate.php',
        ],
        // Custom Discount / Offer Management System
        'discount' => [
            'controller' => 'app/Http/Controllers/CustomDiscountController.php',
            'service' => 'app/Services/CustomDiscountRuleService.php',
            'model' => 'app/CustomDiscount.php',
            'views' => 'resources/views/custom_discounts/',
        ],
        'offer' => [
            'controller' => 'app/Http/Controllers/CustomDiscountController.php',
            'service' => 'app/Services/CustomDiscountRuleService.php',
            'model' => 'app/CustomDiscount.php',
            'views' => 'resources/views/custom_discounts/',
        ],
        'coupon' => [
            'controller' => 'app/Http/Controllers/CustomDiscountController.php',
            'service' => 'app/Services/CustomDiscountRuleService.php',
            'model' => 'app/CustomDiscount.php',
        ],
        'promotion' => [
            'controller' => 'app/Http/Controllers/CustomDiscountController.php',
            'service' => 'app/Services/CustomDiscountRuleService.php',
            'model' => 'app/CustomDiscount.php',
        ],
        'payment' => [
            'controller' => 'app/Http/Controllers/TransactionPaymentController.php',
            'model' => 'app/TransactionPayment.php',
        ],
        'invoice' => [
            'controller' => 'app/Http/Controllers/InvoiceSchemeController.php',
            'model' => 'app/InvoiceScheme.php',
        ],
        'barcode' => [
            'controller' => 'app/Http/Controllers/LabelsController.php',
            'model' => 'app/Barcode.php',
        ],
        'category' => [
            'controller' => 'app/Http/Controllers/TaxonomyController.php',
            'model' => 'app/Category.php',
        ],
        'brand' => [
            'controller' => 'app/Http/Controllers/BrandController.php',
            'model' => 'app/Brands.php',
        ],
        'unit' => [
            'controller' => 'app/Http/Controllers/UnitController.php',
            'model' => 'app/Unit.php',
        ],
        'location' => [
            'controller' => 'app/Http/Controllers/BusinessLocationController.php',
            'model' => 'app/BusinessLocation.php',
        ],
        'register' => [
            'controller' => 'app/Http/Controllers/CashRegisterController.php',
            'model' => 'app/CashRegister.php',
        ],
        'expense' => [
            'controller' => 'app/Http/Controllers/ExpenseController.php',
            'model' => 'app/Transaction.php',
        ],
        'woocommerce' => [
            'controller' => 'Modules/Woocommerce/Http/Controllers/WoocommerceController.php',
            'config' => 'Modules/Woocommerce/Config/config.php',
        ],
        'vendor' => [
            'controller' => 'Modules/Vendor/Http/Controllers/VendorController.php',
            'model' => 'Modules/Vendor/Entities/VendorProductRequest.php',
        ],
        'return' => [
            'controller' => 'app/Http/Controllers/SellReturnController.php',
        ],
        'ticket' => [
            'controller' => 'app/Http/Controllers/TicketController.php',
            'model' => 'app/Ticket.php',
        ],
        'lead' => [
            'controller' => 'app/Http/Controllers/LeadController.php',
            'model' => 'app/Lead.php',
        ],
    ];

    public function __construct()
    {
        $this->basePath = base_path();
        $this->contextDepth = config('supportagent.mcp.context_depth', 3);
        $this->enabled = config('supportagent.mcp.enabled', true);
    }

    /**
     * Check if MCP is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get code context for a user query
     * This is the main method that provides context to the LLM
     *
     * @param string $query
     * @return array
     */
    public function getCodeContext(string $query): array
    {
        if (!$this->enabled) {
            return [
                'enabled' => false,
                'context' => '',
            ];
        }

        $keywords = $this->extractFeatureKeywords($query);
        $relevantFiles = $this->findRelevantFiles($keywords);
        $codeSnippets = $this->extractCodeSnippets($relevantFiles);
        $routeInfo = $this->getRouteInfo($keywords);
        $flowAnalysis = $this->analyzeFeatureFlow($keywords);

        return [
            'enabled' => true,
            'keywords' => $keywords,
            'files' => array_keys($relevantFiles),
            'code_snippets' => $codeSnippets,
            'routes' => $routeInfo,
            'flow' => $flowAnalysis,
            'context' => $this->formatContextForLLM($codeSnippets, $routeInfo, $flowAnalysis),
        ];
    }

    /**
     * Extract feature keywords from user query
     *
     * @param string $query
     * @return array
     */
    protected function extractFeatureKeywords(string $query): array
    {
        $query = strtolower($query);
        $found = [];

        foreach (array_keys($this->featureMap) as $feature) {
            if (Str::contains($query, $feature)) {
                $found[] = $feature;
            }
        }

        // Also check for related terms
        $relatedTerms = [
            'sell' => 'sale',
            'selling' => 'sale',
            'buy' => 'purchase',
            'buying' => 'purchase',
            'client' => 'customer',
            'vendor' => 'supplier',
            'inventory' => 'stock',
            'refund' => 'return',
            'receipt' => 'invoice',
            // Offer Management System related terms
            'offer' => 'offer',
            'promo' => 'promotion',
            'promotional' => 'promotion',
            'coupon' => 'coupon',
            'voucher' => 'coupon',
            'deal' => 'offer',
            'bogo' => 'discount',
            'buy one get one' => 'discount',
            'buyxgetx' => 'discount',
            'buyxgety' => 'discount',
            'free shipping' => 'discount',
            'cart adjustment' => 'discount',
            'product adjustment' => 'discount',
            'percentage off' => 'discount',
            'fixed discount' => 'discount',
        ];

        foreach ($relatedTerms as $term => $feature) {
            if (Str::contains($query, $term) && !in_array($feature, $found)) {
                $found[] = $feature;
            }
        }

        return array_unique($found);
    }

    /**
     * Dynamically search for files in the codebase matching a pattern
     *
     * @param string $searchTerm
     * @return array
     */
    public function dynamicFileSearch(string $searchTerm): array
    {
        $results = [];
        $searchTerm = strtolower($searchTerm);
        
        // Search in controllers
        $controllersPath = $this->basePath . '/app/Http/Controllers';
        if (is_dir($controllersPath)) {
            foreach (File::glob($controllersPath . '/*.php') as $file) {
                if (Str::contains(strtolower(basename($file)), $searchTerm)) {
                    $results['controllers'][] = str_replace($this->basePath . '/', '', $file);
                }
            }
        }
        
        // Search in services
        $servicesPath = $this->basePath . '/app/Services';
        if (is_dir($servicesPath)) {
            foreach (File::glob($servicesPath . '/*.php') as $file) {
                if (Str::contains(strtolower(basename($file)), $searchTerm)) {
                    $results['services'][] = str_replace($this->basePath . '/', '', $file);
                }
            }
        }
        
        // Search in models
        $modelsPath = $this->basePath . '/app/Models';
        if (is_dir($modelsPath)) {
            foreach (File::glob($modelsPath . '/*.php') as $file) {
                if (Str::contains(strtolower(basename($file)), $searchTerm)) {
                    $results['models'][] = str_replace($this->basePath . '/', '', $file);
                }
            }
        }
        
        // Search in app root (legacy models)
        $appPath = $this->basePath . '/app';
        foreach (File::glob($appPath . '/*.php') as $file) {
            if (Str::contains(strtolower(basename($file)), $searchTerm)) {
                $results['models'][] = str_replace($this->basePath . '/', '', $file);
            }
        }
        
        // Search in views
        $viewsPath = $this->basePath . '/resources/views';
        if (is_dir($viewsPath)) {
            $viewDirs = File::directories($viewsPath);
            foreach ($viewDirs as $dir) {
                if (Str::contains(strtolower(basename($dir)), $searchTerm)) {
                    $results['views'][] = str_replace($this->basePath . '/', '', $dir);
                }
            }
        }
        
        return $results;
    }

    /**
     * Find relevant files based on keywords
     *
     * @param array $keywords
     * @return array
     */
    protected function findRelevantFiles(array $keywords): array
    {
        $files = [];

        foreach ($keywords as $keyword) {
            if (isset($this->featureMap[$keyword])) {
                foreach ($this->featureMap[$keyword] as $type => $path) {
                    $fullPath = $this->basePath . '/' . $path;
                    
                    if (is_dir($fullPath)) {
                        // Get first few files from directory
                        $dirFiles = File::glob($fullPath . '/*.php') ?: File::glob($fullPath . '/*.blade.php');
                        foreach (array_slice($dirFiles, 0, 3) as $file) {
                            $files[$file] = $type;
                        }
                    } elseif (File::exists($fullPath)) {
                        $files[$fullPath] = $type;
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Extract relevant code snippets from files
     *
     * @param array $files
     * @return array
     */
    protected function extractCodeSnippets(array $files): array
    {
        $snippets = [];
        $maxLines = 50; // Limit lines per file

        foreach ($files as $filePath => $type) {
            if (!File::exists($filePath)) {
                continue;
            }

            $content = File::get($filePath);
            $relativePath = str_replace($this->basePath . '/', '', $filePath);

            // Extract key parts based on file type
            if ($type === 'controller') {
                $snippet = $this->extractControllerMethods($content);
            } elseif ($type === 'model') {
                $snippet = $this->extractModelInfo($content);
            } elseif ($type === 'routes') {
                $snippet = $this->extractRoutes($content);
            } elseif ($type === 'service') {
                $snippet = $this->extractServiceInfo($content);
            } else {
                // Get first N lines for other files
                $lines = explode("\n", $content);
                $snippet = implode("\n", array_slice($lines, 0, $maxLines));
            }

            $snippets[$relativePath] = [
                'type' => $type,
                'content' => $snippet,
            ];
        }

        return $snippets;
    }

    /**
     * Extract public method signatures from controller
     *
     * @param string $content
     * @return string
     */
    protected function extractControllerMethods(string $content): string
    {
        $methods = [];
        
        // Match public function declarations with docblocks
        preg_match_all(
            '/\/\*\*[\s\S]*?\*\/\s*public\s+function\s+(\w+)\s*\([^)]*\)/m',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $methods[] = $match[0];
        }

        // Also get simple public methods without docblocks
        preg_match_all(
            '/public\s+function\s+(\w+)\s*\([^)]*\)\s*(?:\:\s*\w+)?\s*\{/m',
            $content,
            $simpleMatches,
            PREG_SET_ORDER
        );

        foreach ($simpleMatches as $match) {
            $methodName = $match[1];
            // Check if not already captured
            if (!Str::contains(implode('', $methods), "function {$methodName}")) {
                $methods[] = "public function {$methodName}(...)";
            }
        }

        return implode("\n\n", array_slice($methods, 0, 10));
    }

    /**
     * Extract model relationships and key properties
     *
     * @param string $content
     * @return string
     */
    protected function extractModelInfo(string $content): string
    {
        $info = [];

        // Get class name
        if (preg_match('/class\s+(\w+)\s+extends/', $content, $match)) {
            $info[] = "Model: {$match[1]}";
        }

        // Get fillable
        if (preg_match('/protected\s+\$fillable\s*=\s*\[([\s\S]*?)\];/m', $content, $match)) {
            $info[] = "Fillable: " . preg_replace('/\s+/', ' ', $match[1]);
        }

        // Get relationships
        preg_match_all(
            '/public\s+function\s+(\w+)\s*\(\)\s*(?::\s*\w+)?\s*\{\s*return\s+\$this->(hasMany|belongsTo|hasOne|belongsToMany|morphMany|morphTo)/m',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        // Get relationships
        $relationships = [];
        foreach ($matches as $match) {
            $relationships[] = "{$match[1]} ({$match[2]})";
        }

        if (!empty($relationships)) {
            $info[] = "Relationships: " . implode(', ', $relationships);
        }

        return implode("\n", $info);
    }

    /**
     * Extract service class information including public methods and constants
     *
     * @param string $content
     * @return string
     */
    protected function extractServiceInfo(string $content): string
    {
        $info = [];

        // Get class name
        if (preg_match('/class\s+(\w+)(?:\s+extends|\s*\{)/', $content, $match)) {
            $info[] = "Service: {$match[1]}";
        }

        // Get constants (often define types/modes)
        preg_match_all(
            '/(?:public|protected|private)?\s*const\s+(\w+)\s*=\s*([^;]+);/m',
            $content,
            $constMatches,
            PREG_SET_ORDER
        );

        if (!empty($constMatches)) {
            $constants = [];
            foreach (array_slice($constMatches, 0, 15) as $match) {
                $constants[] = "{$match[1]} = {$match[2]}";
            }
            $info[] = "\nConstants/Types:\n" . implode("\n", $constants);
        }

        // Get public method signatures with their first line of logic
        preg_match_all(
            '/public\s+(?:static\s+)?function\s+(\w+)\s*\(([^)]*)\)(?:\s*:\s*(\??\w+))?\s*\{/m',
            $content,
            $methodMatches,
            PREG_SET_ORDER
        );

        if (!empty($methodMatches)) {
            $methods = [];
            foreach (array_slice($methodMatches, 0, 20) as $match) {
                $returnType = isset($match[3]) ? ": {$match[3]}" : '';
                $params = $this->simplifyParameters($match[2]);
                $methods[] = "- {$match[1]}({$params}){$returnType}";
            }
            $info[] = "\nPublic Methods:\n" . implode("\n", $methods);
        }

        // For CustomDiscountRuleService, extract discount types from the code
        if (preg_match_all('/[\'"]discountType[\'"]\s*(?:=>|:)\s*[\'"](\w+)[\'"]/', $content, $typeMatches)) {
            $types = array_unique($typeMatches[1]);
            $info[] = "\nDiscount Types Found: " . implode(', ', $types);
        }

        // Extract validation rules or switch cases that define behavior
        if (preg_match_all('/case\s+[\'"](\w+)[\'"]\s*:/m', $content, $caseMatches)) {
            $cases = array_unique($caseMatches[1]);
            if (count($cases) > 0) {
                $info[] = "\nSwitch Cases (behaviors): " . implode(', ', array_slice($cases, 0, 15));
            }
        }

        return implode("\n", $info);
    }

    /**
     * Simplify parameter list for display
     *
     * @param string $params
     * @return string
     */
    protected function simplifyParameters(string $params): string
    {
        if (empty(trim($params))) {
            return '';
        }
        
        // Get just parameter names without type hints
        $params = preg_replace('/\??\w+\s+/', '', $params);
        $params = preg_replace('/\s*=\s*[^,]+/', '', $params);
        $params = preg_replace('/\s+/', ' ', trim($params));
        
        // Truncate if too long
        if (strlen($params) > 50) {
            return '...';
        }
        
        return $params;
    }

    /**
     * Extract route definitions
     *
     * @param string $content
     * @return string
     */
    protected function extractRoutes(string $content): string
    {
        $routes = [];
        
        // Match route definitions
        preg_match_all(
            '/Route::(get|post|put|patch|delete)\s*\(\s*[\'"]([^\'"]+)[\'"]/m',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach (array_slice($matches, 0, 20) as $match) {
            $routes[] = strtoupper($match[1]) . ' ' . $match[2];
        }

        return implode("\n", $routes);
    }

    /**
     * Get route information for features
     *
     * @param array $keywords
     * @return array
     */
    protected function getRouteInfo(array $keywords): array
    {
        $routeMap = [
            'pos' => [
                'GET /pos/create' => 'Open POS interface',
                'POST /pos/store' => 'Complete sale',
                'GET /pos/{id}/edit' => 'Edit existing sale',
            ],
            'sale' => [
                'GET /sells' => 'List all sales',
                'GET /sells/create' => 'Create new sale',
                'POST /sells' => 'Store sale',
            ],
            'product' => [
                'GET /products' => 'List products',
                'GET /products/create' => 'Add new product',
                'POST /products' => 'Store product',
                'GET /products/{id}/edit' => 'Edit product',
            ],
            'contact' => [
                'GET /contacts' => 'List contacts',
                'GET /contacts/customers' => 'List customers',
                'GET /contacts/suppliers' => 'List suppliers',
            ],
            'stock' => [
                'GET /stock-adjustment' => 'Stock adjustments list',
                'GET /stock-transfers' => 'Stock transfers',
            ],
            'report' => [
                'GET /reports/profit-loss' => 'Profit/Loss report',
                'GET /reports/product-sell-report' => 'Product sales report',
                'GET /reports/stock-report' => 'Stock report',
            ],
            'purchase' => [
                'GET /purchases' => 'List purchases',
                'GET /purchases/create' => 'Create purchase order',
            ],
            'discount' => [
                'GET /custom-discounts' => 'List all offers (Offer Management System)',
                'GET /custom-discounts/create' => 'Create new offer/discount',
                'POST /custom-discounts' => 'Store new offer',
                'GET /custom-discounts/{id}/edit' => 'Edit existing offer',
                'PUT /custom-discounts/{id}' => 'Update offer',
                'DELETE /custom-discounts/{id}' => 'Delete offer',
            ],
            'offer' => [
                'GET /custom-discounts' => 'Offer Management System index',
                'GET /custom-discounts/create' => 'Create new promotional offer',
            ],
            'coupon' => [
                'GET /custom-discounts' => 'Coupon management via Offer Management System',
                'POST /custom-discounts' => 'Create new coupon (set couponCode field)',
            ],
            'promotion' => [
                'GET /custom-discounts' => 'Promotional offers list',
                'GET /custom-discounts/create' => 'Create promotional offer',
            ],
        ];

        $routes = [];
        foreach ($keywords as $keyword) {
            if (isset($routeMap[$keyword])) {
                $routes[$keyword] = $routeMap[$keyword];
            }
        }

        return $routes;
    }

    /**
     * Analyze feature flow (how features work)
     *
     * @param array $keywords
     * @return array
     */
    protected function analyzeFeatureFlow(array $keywords): array
    {
        $flows = [
            'pos' => [
                'name' => 'Point of Sale Flow',
                'steps' => [
                    '1. User navigates to POS > Create (SellPosController@create)',
                    '2. Page loads with products, customers, and payment options',
                    '3. User adds products to cart (JavaScript updates DOM)',
                    '4. User selects customer or uses Walk-in Customer',
                    '5. User clicks Pay button to open payment modal',
                    '6. User selects payment method and enters amount',
                    '7. Form submits to SellPosController@store',
                    '8. TransactionUtil handles transaction creation',
                    '9. Stock is updated via ProductUtil',
                    '10. Receipt is generated and displayed',
                ],
                'key_files' => [
                    'Controller: app/Http/Controllers/SellPosController.php',
                    'Utils: app/Utils/TransactionUtil.php, app/Utils/ProductUtil.php',
                    'Model: app/Transaction.php, app/TransactionSellLine.php',
                    'View: resources/views/sale_pos/create.blade.php',
                ],
            ],
            'sale' => [
                'name' => 'Sales Flow',
                'steps' => [
                    '1. Navigate to Sales > Add Sale (SellController@create)',
                    '2. Select customer and add products',
                    '3. Apply discounts if needed',
                    '4. Choose payment terms (pay now or credit)',
                    '5. Submit form (SellController@store)',
                    '6. Transaction is created with type "sell"',
                    '7. Stock is deducted from inventory',
                ],
            ],
            'product' => [
                'name' => 'Product Management Flow',
                'steps' => [
                    '1. Navigate to Products > Add Product',
                    '2. Fill required fields: Name, SKU, Category, Unit',
                    '3. Set pricing: Purchase price, Selling price',
                    '4. For variations: Enable "This product has variations"',
                    '5. Add variation attributes and prices',
                    '6. Submit form (ProductController@store)',
                    '7. Product is created with associated variations',
                ],
            ],
            'stock' => [
                'name' => 'Stock Management Flow',
                'steps' => [
                    '1. Stock Adjustment: Inventory > Stock Adjustment',
                    '2. Select location and adjustment type (add/remove)',
                    '3. Add products and quantities',
                    '4. Submit to update stock levels',
                    '5. Stock Transfer: Inventory > Stock Transfer',
                    '6. Select source and destination locations',
                    '7. Add products to transfer',
                    '8. Submit to move stock between locations',
                ],
            ],
            'return' => [
                'name' => 'Sales Return Flow',
                'steps' => [
                    '1. Go to Sales > List Sales',
                    '2. Find the original sale',
                    '3. Click Return/Refund action',
                    '4. Select items to return',
                    '5. Enter return quantities',
                    '6. Choose refund method',
                    '7. Submit (SellReturnController@store)',
                    '8. Stock is automatically added back',
                ],
            ],
            'discount' => [
                'name' => 'Offer Management System',
                'steps' => [
                    '1. Go to Offer Management System in the sidebar menu',
                    '2. Click "CREATE A NEW" button (top right with plus icon)',
                    '3. Choose the Discount Type:',
                    '   - Product Discount: Apply discount to specific products',
                    '   - Cart Discount: Apply discount to entire order total',
                    '   - Free Shipping: Waive shipping charges',
                    '   - Buy X Get X Free: Buy items, get same items free (BOGO)',
                    '   - Buy X Get Y Free: Buy items, get different items free',
                    '4. Set the Discount Value:',
                    '   - Percentage Off: e.g., 20% discount',
                    '   - Fixed Amount Off: e.g., $10 off',
                    '   - Fixed Price: Set a specific price for items',
                    '   - Free: Make items completely free',
                    '5. Apply Filters (optional):',
                    '   - Limit to specific categories or exclude categories',
                    '   - Limit to specific brands or exclude brands',
                    '   - Limit to specific products or variations',
                    '6. Set Customer Rules:',
                    '   - Apply to: All customers, specific groups, or selected customers',
                    '   - First order only option for new customer promotions',
                    '   - Minimum lifetime spend requirement',
                    '7. Set Cart Rules:',
                    '   - Minimum order value to qualify',
                    '   - Maximum discount cap',
                    '8. Add Coupon Code (optional) - customers enter at checkout',
                    '9. Set Start Date and End Date for the offer validity',
                    '10. Click Save to activate your offer',
                ],
            ],
            'offer' => [
                'name' => 'Offer Management System Overview',
                'steps' => [
                    '1. Find Offer Management System in the sidebar menu',
                    '2. View all your offers in a table showing status, type, and dates',
                    '3. Click "CREATE A NEW" to add a new promotional offer',
                    '4. Configure your discount type, value, and rules',
                    '5. Offers are automatically applied during checkout when conditions are met',
                ],
            ],
            'coupon' => [
                'name' => 'Coupon Codes',
                'steps' => [
                    '1. Create a new offer in Offer Management System',
                    '2. Fill in the Coupon Code field with your desired code',
                    '3. Share the code with your customers',
                    '4. Customers enter the code at checkout',
                    '5. System validates: code validity, expiry, customer eligibility, cart requirements',
                    '6. If valid, discount is applied to the order',
                    '7. Referral coupons can also be set up for customer referral programs',
                ],
            ],
        ];

        $relevantFlows = [];
        foreach ($keywords as $keyword) {
            if (isset($flows[$keyword])) {
                $relevantFlows[$keyword] = $flows[$keyword];
            }
        }

        return $relevantFlows;
    }

    /**
     * Format context for LLM consumption (internal knowledge)
     * This provides understanding of features without exposing code
     *
     * @param array $codeSnippets
     * @param array $routeInfo
     * @param array $flowAnalysis
     * @return string
     */
    protected function formatContextForLLM(array $codeSnippets, array $routeInfo, array $flowAnalysis): string
    {
        $context = "## Feature Knowledge (Internal - Do not share with users)\n\n";

        // Add flow analysis - focus on user steps, not code
        if (!empty($flowAnalysis)) {
            $context .= "### How Features Work:\n";
            foreach ($flowAnalysis as $feature => $flow) {
                $context .= "\n**{$flow['name']}**\n";
                // Filter out technical references for user-facing steps
                $userFriendlySteps = [];
                foreach ($flow['steps'] as $step) {
                    // Remove controller/method references for cleaner guidance
                    $cleanStep = preg_replace('/\s*\([A-Za-z]+Controller@\w+\)/', '', $step);
                    $cleanStep = preg_replace('/\s*\([A-Za-z]+Service::\w+\(\)\)/', '', $step);
                    $userFriendlySteps[] = $cleanStep;
                }
                $context .= implode("\n", $userFriendlySteps) . "\n";
            }
        }

        // Add navigation info (not raw routes)
        if (!empty($routeInfo)) {
            $context .= "\n### Navigation Paths:\n";
            foreach ($routeInfo as $feature => $routes) {
                $context .= "\n**{$feature}:**\n";
                foreach ($routes as $route => $description) {
                    // Convert route to user-friendly navigation
                    $navPath = $this->routeToNavigation($route);
                    $context .= "- {$description} ({$navPath})\n";
                }
            }
        }

        // Add feature capabilities extracted from code (not the code itself)
        if (!empty($codeSnippets)) {
            $context .= "\n### Feature Capabilities (extracted from codebase):\n";
            foreach (array_slice($codeSnippets, 0, 5) as $file => $info) {
                $capabilities = $this->extractCapabilities($info['content'], $info['type']);
                if (!empty($capabilities)) {
                    $featureName = $this->fileToFeatureName($file);
                    $context .= "\n**{$featureName}:**\n";
                    $context .= $capabilities . "\n";
                }
            }
        }

        return $context;
    }

    /**
     * Convert route to user-friendly navigation
     *
     * @param string $route
     * @return string
     */
    protected function routeToNavigation(string $route): string
    {
        // Remove HTTP method prefix
        $route = preg_replace('/^(GET|POST|PUT|PATCH|DELETE)\s+/', '', $route);
        
        // Convert route segments to menu path
        $segments = explode('/', trim($route, '/'));
        $navParts = [];
        
        foreach ($segments as $segment) {
            if (preg_match('/^\{/', $segment)) {
                continue; // Skip URL parameters
            }
            $navParts[] = ucwords(str_replace('-', ' ', $segment));
        }
        
        return empty($navParts) ? 'Dashboard' : implode(' > ', $navParts);
    }

    /**
     * Convert file path to feature name
     *
     * @param string $filePath
     * @return string
     */
    protected function fileToFeatureName(string $filePath): string
    {
        $fileName = basename($filePath, '.php');
        
        // Remove common suffixes
        $fileName = preg_replace('/(Controller|Service|Model|Util)$/', '', $fileName);
        
        // Convert camelCase to words
        $name = preg_replace('/([a-z])([A-Z])/', '$1 $2', $fileName);
        
        return ucwords($name);
    }

    /**
     * Extract feature capabilities from code content
     *
     * @param string $content
     * @param string $type
     * @return string
     */
    protected function extractCapabilities(string $content, string $type): string
    {
        $capabilities = [];
        
        if ($type === 'service') {
            // Extract discount types
            if (preg_match_all('/[\'"]discountType[\'"]\s*(?:=>|:)\s*[\'"](\w+)[\'"]/', $content, $matches)) {
                $types = array_unique($matches[1]);
                $friendlyTypes = [];
                foreach ($types as $t) {
                    $friendlyTypes[] = $this->technicalToFriendly($t);
                }
                $capabilities[] = "Available discount types: " . implode(', ', $friendlyTypes);
            }
            
            // Extract discount value types
            if (preg_match_all('/[\'"]discount[\'"]\s*(?:=>|:)\s*[\'"](\w+)[\'"]/', $content, $matches)) {
                $types = array_unique($matches[1]);
                $friendlyTypes = [];
                foreach ($types as $t) {
                    $friendlyTypes[] = $this->technicalToFriendly($t);
                }
                if (!empty($friendlyTypes)) {
                    $capabilities[] = "Discount value options: " . implode(', ', $friendlyTypes);
                }
            }
        }
        
        if ($type === 'controller') {
            // Count available actions
            preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $matches);
            $actions = count($matches[1] ?? []);
            if ($actions > 0) {
                $capabilities[] = "This feature has {$actions} available actions";
            }
            
            // Check for common CRUD operations
            $crudMethods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
            $foundCrud = [];
            foreach ($crudMethods as $method) {
                if (in_array($method, $matches[1] ?? [])) {
                    $foundCrud[] = $method;
                }
            }
            if (!empty($foundCrud)) {
                $capabilities[] = "Supports: list, create, view, edit, and delete operations";
            }
        }
        
        return implode("\n", $capabilities);
    }

    /**
     * Convert technical term to user-friendly text
     *
     * @param string $term
     * @return string
     */
    protected function technicalToFriendly(string $term): string
    {
        $map = [
            'productAdjustment' => 'Product Discount',
            'cartAdjustment' => 'Cart Discount',
            'freeShipping' => 'Free Shipping',
            'buyXgetX' => 'Buy X Get X Free (BOGO)',
            'buyXgetY' => 'Buy X Get Y Free',
            'percentageDiscount' => 'Percentage Off',
            'fixedDiscount' => 'Fixed Amount Off',
            'fixedPricePerItem' => 'Fixed Price',
            'free' => 'Free Item',
        ];
        
        return $map[$term] ?? ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', $term));
    }

    /**
     * Get the structure of the entire codebase (cached)
     *
     * @return array
     */
    public function getCodebaseStructure(): array
    {
        return Cache::remember('mcp_codebase_structure', 3600, function () {
            $structure = [
                'controllers' => $this->scanDirectory('app/Http/Controllers'),
                'models' => $this->scanDirectory('app', 1),
                'modules' => $this->scanModules(),
                'routes' => $this->scanDirectory('routes'),
            ];

            return $structure;
        });
    }

    /**
     * Scan a directory for PHP files
     *
     * @param string $path
     * @param int $depth
     * @return array
     */
    protected function scanDirectory(string $path, int $depth = 2): array
    {
        $fullPath = $this->basePath . '/' . $path;
        
        if (!is_dir($fullPath)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        $iterator->setMaxDepth($depth);

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace($this->basePath . '/', '', $file->getPathname());
                $files[] = $relativePath;
            }
        }

        return $files;
    }

    /**
     * Scan installed modules
     *
     * @return array
     */
    protected function scanModules(): array
    {
        $modulesPath = $this->basePath . '/Modules';
        
        if (!is_dir($modulesPath)) {
            return [];
        }

        $modules = [];
        $dirs = File::directories($modulesPath);

        foreach ($dirs as $dir) {
            $moduleName = basename($dir);
            $modules[$moduleName] = [
                'controllers' => $this->scanDirectory("Modules/{$moduleName}/Http/Controllers", 1),
                'has_views' => is_dir("{$dir}/Resources/views"),
                'has_routes' => is_dir("{$dir}/Routes"),
            ];
        }

        return $modules;
    }
}
