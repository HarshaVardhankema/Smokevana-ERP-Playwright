<?php

namespace Modules\SupportAgent\Services;

use Illuminate\Support\Facades\Cache;

class FeatureDocumentationService
{
    /**
     * Get documentation for a specific feature based on user query
     *
     * @param string $query
     * @return string
     */
    public function getRelevantDocumentation(string $query): string
    {
        $keywords = $this->extractKeywords($query);
        $relevantDocs = [];

        foreach ($keywords as $keyword) {
            $docs = $this->findDocumentationByKeyword($keyword);
            $relevantDocs = array_merge($relevantDocs, $docs);
        }

        // Remove duplicates and limit
        $relevantDocs = array_unique($relevantDocs);
        
        return implode("\n\n", array_slice($relevantDocs, 0, 3));
    }

    /**
     * Extract keywords from query
     *
     * @param string $query
     * @return array
     */
    protected function extractKeywords(string $query): array
    {
        $query = strtolower($query);
        
        // Common ERP terms to look for
        $erpTerms = [
            'sale', 'sell', 'pos', 'product', 'inventory', 'stock', 'purchase',
            'customer', 'contact', 'supplier', 'vendor', 'report', 'invoice',
            'payment', 'discount', 'tax', 'category', 'brand', 'variation',
            'user', 'role', 'permission', 'location', 'register', 'receipt',
            'return', 'refund', 'adjustment', 'transfer', 'order', 'quotation',
            'expense', 'account', 'dashboard', 'setting', 'import', 'export',
            'barcode', 'label', 'print', 'ticket', 'complaint', 'lead',
            'woocommerce', 'ecommerce', 'sync', 'module', 'subscription',
            // Offer Management System terms
            'offer', 'coupon', 'promotion', 'promo', 'deal', 'bogo',
            'buyxgetx', 'buyxgety', 'free shipping', 'cart adjustment',
            'product adjustment', 'percentage', 'fixed discount',
        ];

        $foundKeywords = [];
        
        foreach ($erpTerms as $term) {
            if (stripos($query, $term) !== false) {
                $foundKeywords[] = $term;
            }
        }

        // Map common phrases to keywords
        $phraseMap = [
            'offer management' => 'offer',
            'custom discount' => 'discount',
            'promotional offer' => 'promotion',
            'buy one get one' => 'discount',
            'free delivery' => 'discount',
        ];
        
        foreach ($phraseMap as $phrase => $keyword) {
            if (stripos($query, $phrase) !== false && !in_array($keyword, $foundKeywords)) {
                $foundKeywords[] = $keyword;
            }
        }

        return $foundKeywords;
    }

    /**
     * Find documentation by keyword
     *
     * @param string $keyword
     * @return array
     */
    protected function findDocumentationByKeyword(string $keyword): array
    {
        $documentation = $this->getDocumentationLibrary();
        
        return $documentation[$keyword] ?? [];
    }

    /**
     * Get the full documentation library
     *
     * @return array
     */
    public function getDocumentationLibrary(): array
    {
        return Cache::remember('support_agent_docs', 3600, function () {
            return [
                'sale' => [
                    "## Creating a Sale\n- Navigate to: POS > Create Sale (or press F7)\n- Select customer or use 'Walk-in Customer'\n- Search and add products using barcode or name\n- Apply discounts if needed\n- Select payment method and complete sale\n- Print or email receipt",
                ],
                'sell' => [
                    "## Point of Sale (POS)\n- Quick sale mode: POS > Create\n- Add products by scanning barcode or searching\n- Use keyboard shortcuts: F1-F9 for quick actions\n- Suspend sale: Save current sale for later\n- Multiple payment methods supported",
                ],
                'pos' => [
                    "## POS Features\n- **Quick Keys**: Set up frequent products for one-click adding\n- **Suspend/Resume**: Save sale for later (F5)\n- **Discount**: Apply order-level or item-level discounts\n- **Payment Split**: Accept multiple payment methods\n- **Register Management**: Open/close with cash counting\n- **Keyboard Shortcuts**: F1(Pay), F2(Add Payment), F3(Print), F4(Cancel), F5(Suspend), F7(New Sale)",
                ],
                'product' => [
                    "## Product Management\n- **Add Product**: Products > Add New\n- **Required Fields**: Name, SKU, Category, Unit\n- **Pricing**: Set selling price, purchase price, margins\n- **Variations**: Create size/color/attribute variations\n- **Stock**: Manage quantity per location\n- **Bulk Import**: Products > Import via CSV",
                    "## Product Variations\n- Navigate to Products > Add New\n- Enable 'Variable Product' option\n- Add variation attributes (size, color, etc.)\n- Set individual prices and SKUs for each variation\n- Manage stock per variation",
                ],
                'inventory' => [
                    "## Inventory Management\n- **View Stock**: Inventory > Stock Report\n- **Low Stock Alerts**: Automatic notifications\n- **Stock Adjustment**: Inventory > Stock Adjustment\n- **Stock Transfer**: Move between locations\n- **Opening Stock**: Set initial quantities",
                ],
                'stock' => [
                    "## Stock Operations\n- **Check Stock**: Products > View > Stock tab\n- **Adjust Stock**: Inventory > Stock Adjustment (add/remove)\n- **Transfer Stock**: Inventory > Stock Transfer\n- **Stock Reports**: Reports > Stock Report\n- **Low Stock**: Reports > Low Stock Products",
                ],
                'customer' => [
                    "## Customer Management\n- **Add Customer**: Contacts > Customers > Add\n- **Required Info**: Name, Mobile, Address\n- **Customer Groups**: Apply group discounts\n- **Credit Limit**: Set purchase limits\n- **Purchase History**: View from customer profile\n- **Loyalty Points**: Enable in business settings",
                ],
                'contact' => [
                    "## Contact Types\n- **Customers**: End buyers of your products\n- **Suppliers**: Product vendors for purchasing\n- **Both**: Contacts who are both customer and supplier\n- **Leads**: Potential customers for CRM\n- All contacts can have: Address, Tax info, Credit terms",
                ],
                'supplier' => [
                    "## Supplier Management\n- **Add Supplier**: Contacts > Suppliers > Add\n- **Create Purchase**: Purchase > Add Purchase\n- **Track Payments**: Due payments management\n- **Supplier Products**: Assign products to suppliers\n- **Contact Info**: Store multiple addresses",
                ],
                'report' => [
                    "## Available Reports\n- **Sales Report**: Daily, weekly, monthly sales\n- **Stock Report**: Current inventory levels\n- **Profit/Loss**: Revenue and expenses analysis\n- **Tax Report**: Tax collected summary\n- **Customer Report**: Top customers, dues\n- **Trending Products**: Best/worst sellers\n- Export: All reports can be exported to Excel/PDF",
                ],
                'invoice' => [
                    "## Invoice Management\n- **Create Invoice**: Sales > Add Sale\n- **Invoice Layout**: Settings > Invoice Settings\n- **Print Invoice**: From sale details page\n- **Email Invoice**: Send PDF to customer\n- **Invoice Scheme**: Customize numbering format",
                ],
                'payment' => [
                    "## Payment Methods\n- **Cash**: Standard cash payment\n- **Card**: Credit/Debit card payments\n- **Bank Transfer**: Direct bank payments\n- **Custom Methods**: Add your own in Settings\n- **Split Payment**: Use multiple methods per sale\n- **Payment Accounts**: Track where money goes",
                ],
                'discount' => [
                    "## Offer Management System (Custom Discounts)\nNavigate to: Offer Management System in sidebar\nController: CustomDiscountController\nService: CustomDiscountRuleService\nView: resources/views/custom_discounts/\n\n### Discount Types (discountType):\n- **productAdjustment**: Discount on specific products/variations\n- **cartAdjustment**: Discount on entire cart total\n- **freeShipping**: Free shipping when conditions met\n- **buyXgetX**: Buy X items, get same items free (BOGO)\n- **buyXgetY**: Buy X items, get Y different items free\n\n### Discount Value Types (discount):\n- **percentageDiscount**: Percentage off (e.g., 20%)\n- **fixedDiscount**: Fixed amount off (e.g., $10)\n- **fixedPricePerItem**: Set fixed price for item\n- **free**: Item is completely free\n\n### Filter Options:\n- **categories/not_categories**: Target/exclude specific categories\n- **brand/not_brand**: Target/exclude specific brands\n- **product_ids/not_product_ids**: Target/exclude specific products\n- **variation_ids/not_variation_ids**: Target/exclude specific variations\n\n### Customer Rules (rulesOnCustomer):\n- **applyOn**: all, customer-group, customer-list\n- **on-first-order**: Only for first-time customers\n- **on-last-order-value**: Minimum lifetime order value required\n\n### Cart Rules (rulesOnCart):\n- **minOrderValue**: Minimum cart value to qualify\n- **maxDiscountAmount**: Maximum discount cap\n\n### Coupon Codes:\n- Set couponCode field to create redeemable codes\n- Validated via CustomDiscountRuleService::validateCoupon()\n- Supports referral coupons via EcomReferalProgram",
                ],
                'offer' => [
                    "## Offer Management System\nThis is the main promotional system in Smokevana ERP.\n- Navigate to: Offer Management System (sidebar)\n- URL: /custom-discounts\n- Create offers: Click 'CREATE A NEW' button\n- Configure discount type, value, filters, and rules\n- All discounts processed by CustomDiscountRuleService",
                ],
                'coupon' => [
                    "## Coupon Codes\n- Created within Offer Management System\n- Set couponCode field when creating discount\n- Customer enters code at checkout\n- Validated by CustomDiscountRuleService::validateCoupon()\n- Supports: Product discounts, Cart discounts, Free shipping, BOGO offers",
                ],
                'promotion' => [
                    "## Promotions / Offers\n- All handled by Offer Management System\n- Access via sidebar menu\n- Types: Product Adjustment, Cart Adjustment, Free Shipping, Buy X Get X, Buy X Get Y\n- Set validity dates: applyDate and endDate\n- Target specific customers, groups, or all customers",
                ],
                'tax' => [
                    "## Tax Configuration\n- **Add Tax Rate**: Settings > Tax Rates\n- **Tax Groups**: Combine multiple taxes\n- **Product Tax**: Assign per product\n- **Location Tax**: Default tax per location\n- **Inclusive/Exclusive**: Price includes tax or adds\n- **Tax Reports**: View in Reports section",
                ],
                'user' => [
                    "## User Management\n- **Add User**: Settings > Users > Add\n- **Roles**: Define permission sets\n- **Permissions**: Granular access control\n- **Locations**: Restrict by business location\n- **Commission**: Set sales commission rules\n- **Activity Log**: Track user actions",
                ],
                'role' => [
                    "## Roles & Permissions\n- **Create Role**: Settings > Roles > Add\n- **Assign Permissions**: Select allowed actions\n- **Module Access**: Control which modules visible\n- **Location Access**: Limit by locations\n- **Default Roles**: Admin, Cashier, Manager templates",
                ],
                'return' => [
                    "## Processing Returns\n- **From POS**: Use Return button on sale\n- **Sell Return**: Sales > List > Return action\n- **Partial Return**: Return specific items only\n- **Refund Options**: Cash, store credit, or exchange\n- **Stock Update**: Automatically restocks returned items",
                ],
                'refund' => [
                    "## Refund Process\n1. Go to Sales > List\n2. Find the original sale\n3. Click Return/Refund\n4. Select items to refund\n5. Choose refund method\n6. Complete refund\n- Stock is automatically updated",
                ],
                'import' => [
                    "## Bulk Import\n- **Products**: Products > Import Products\n- **Contacts**: Contacts > Import Contacts\n- **Stock**: Inventory > Import Opening Stock\n- **Format**: Download sample CSV template\n- **Validation**: System checks for errors before import",
                ],
                'export' => [
                    "## Export Data\n- **Reports**: Use Export buttons (Excel, PDF, Print)\n- **Products**: Products > Export\n- **Contacts**: Contacts > Export\n- **Transactions**: From any list page\n- All tables with DataTables have export options",
                ],
                'barcode' => [
                    "## Barcode Features\n- **Print Labels**: Products > Print Labels\n- **Barcode Settings**: Settings > Barcode Settings\n- **Label Sizes**: Customize dimensions\n- **Bulk Print**: Print for multiple products\n- **Scan in POS**: Quick product lookup",
                ],
                'dashboard' => [
                    "## Dashboard Widgets\n- **Sales Overview**: Today/Week/Month totals\n- **Top Products**: Best selling items\n- **Stock Alerts**: Low stock notifications\n- **Recent Activity**: Latest transactions\n- **Customize**: Drag widgets to rearrange",
                ],
                'woocommerce' => [
                    "## WooCommerce Integration\n- **Setup**: Modules > WooCommerce > Settings\n- **Connect Store**: Enter API credentials\n- **Sync Products**: Push/Pull products\n- **Sync Orders**: Import WooCommerce orders\n- **Stock Sync**: Keep inventory in sync\n- **Category Mapping**: Map to local categories",
                ],
                'vendor' => [
                    "## Vendor Portal\n- **Vendor Access**: Separate login for suppliers\n- **Product Requests**: Vendors request to sell products\n- **Approval Workflow**: Admin approves/rejects\n- **API Access**: Vendors can integrate via API\n- **Portal Setup**: Modules > Vendor settings",
                ],
                'module' => [
                    "## Modules\n- **Available Modules**: Essentials, CRM, Ecommerce, etc.\n- **Enable/Disable**: Settings > Modules\n- **Module Features**: Each adds specific functionality\n- **Subscription**: Some modules require subscription",
                ],
                'location' => [
                    "## Business Locations\n- **Add Location**: Settings > Business Locations\n- **Location Settings**: Tax, invoice, payment methods\n- **Multi-Location**: Manage inventory per location\n- **User Access**: Assign users to locations\n- **Transfer**: Stock transfer between locations",
                ],
                'register' => [
                    "## Cash Register\n- **Open Register**: POS > Cash Register\n- **Count Cash**: Enter denominations\n- **Close Register**: End of day closing\n- **Register Report**: View shift sales\n- **Multiple Registers**: Different registers per location",
                ],
            ];
        });
    }

    /**
     * Get all available feature categories
     *
     * @return array
     */
    public function getFeatureCategories(): array
    {
        return [
            'sales' => ['sale', 'sell', 'pos', 'invoice', 'payment', 'discount', 'return', 'refund'],
            'inventory' => ['product', 'inventory', 'stock', 'barcode', 'import', 'export'],
            'contacts' => ['customer', 'contact', 'supplier', 'vendor'],
            'settings' => ['user', 'role', 'tax', 'location', 'register', 'module'],
            'reports' => ['report', 'dashboard'],
            'integrations' => ['woocommerce', 'ecommerce'],
        ];
    }

    /**
     * Get feature navigation paths
     *
     * @return array
     */
    public function getNavigationPaths(): array
    {
        return [
            'pos_create' => 'POS > Create Sale (Ctrl+Shift+P or F7)',
            'product_add' => 'Products > Add Product',
            'product_list' => 'Products > List Products',
            'customer_add' => 'Contacts > Customers > Add',
            'supplier_add' => 'Contacts > Suppliers > Add',
            'purchase_add' => 'Purchase > Add Purchase',
            'sale_list' => 'Sales > List Sales',
            'stock_adjustment' => 'Inventory > Stock Adjustment',
            'stock_transfer' => 'Inventory > Stock Transfer',
            'reports_sales' => 'Reports > Sales Report',
            'reports_stock' => 'Reports > Stock Report',
            'settings_business' => 'Settings > Business Settings',
            'settings_tax' => 'Settings > Tax Rates',
            'settings_users' => 'Settings > Users',
            'settings_roles' => 'Settings > Roles',
            'settings_locations' => 'Settings > Business Locations',
        ];
    }
}
