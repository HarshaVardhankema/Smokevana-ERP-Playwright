import { Locator, Page } from "@playwright/test";

export class EditSellingPricePage {
    readonly page: Page;
    readonly productsMenu: Locator;
    readonly editSellingPriceMenu: Locator;
    readonly filterLocation: Locator;
    readonly filterProductType: Locator;
    readonly filterBrand: Locator;
    readonly applyButton: Locator;

    // Price inputs
    readonly costInput: Locator;
    readonly sellingPriceInput: Locator;
    readonly primeInput: Locator;
    readonly diamondPriceInput: Locator;
    readonly lowestPriceInput: Locator;
    readonly platinumPriceInput: Locator;
    readonly goldPriceInput: Locator;
    readonly silverPriceInput: Locator;

    readonly applyToAllBtn: Locator;
    readonly selectAllCheckbox: Locator;
    readonly saveBtn: Locator;

    constructor(page: Page) {
        this.page = page;
        this.productsMenu = page.getByRole('link', { name: 'Products', exact: true });
        this.editSellingPriceMenu = page.getByRole('link', { name: 'Edit Selling Price' });

        // Filters
        this.filterLocation = page.locator('#select2-filter_location_id-container');
        this.filterProductType = page.locator('#select2-filter_product_type-container');
        this.filterBrand = page.locator('#select2-filter_brand_id-container');
        this.applyButton = page.getByRole('button', { name: ' Apply' });

        // Prices
        this.costInput = page.getByRole('textbox', { name: 'Cost:' });
        this.sellingPriceInput = page.getByRole('textbox', { name: 'Selling Price:' });
        this.primeInput = page.getByRole('textbox', { name: 'Prime:' }).first(); // Handle overlaps safely if there are multiples
        this.diamondPriceInput = page.getByRole('textbox', { name: 'DiamondSellingPrice:' });
        this.lowestPriceInput = page.getByRole('textbox', { name: 'LowestSellingPrice:' });
        this.platinumPriceInput = page.getByRole('textbox', { name: 'PlatinumSellingPrice:' });
        this.goldPriceInput = page.getByRole('textbox', { name: 'GoldSellingPrice:' });
        this.silverPriceInput = page.getByRole('textbox', { name: 'SilverSellingPrice:' });

        this.applyToAllBtn = page.getByRole('button', { name: ' Apply to All' });
        this.selectAllCheckbox = page.getByRole('checkbox', { name: 'Select All' });
        this.saveBtn = page.getByRole('button', { name: 'Save' });
    }

    async navigate() {
        // Navigating via the menus
        if (!await this.editSellingPriceMenu.isVisible()) {
            await this.productsMenu.first().click({ force: true }); // Open dropdown if needed
        }
        await this.editSellingPriceMenu.click({ force: true });
    }

    async applyFilters(location: string, productType: string, brand: string) {
        // Location Filter
        await this.filterLocation.click();
        await this.page.getByRole('treeitem', { name: location }).click();

        // Product Type Filter
        await this.filterProductType.click();
        // Fallback for getting nth like the script if exact match isn't available, but exact name works better:
        await this.page.getByRole('treeitem', { name: productType }).nth(1).click({ force: true });

        // Brand Filter using search functionality
        await this.filterBrand.click();
        await this.page.locator('input[type="search"]').fill(brand.substring(0, 3)); // Type start of brand
        await this.page.getByRole('treeitem', { name: brand }).click();

        // Apply Filters
        await this.applyButton.click();
    }

    async fillPrices(prices: {
        cost: string, sellingPrice: string, prime: string, diamond: string,
        lowest: string, platinum: string, gold: string, silver: string
    }) {
        await this.costInput.fill(prices.cost);
        await this.sellingPriceInput.fill(prices.sellingPrice);
        await this.primeInput.fill(prices.prime);
        await this.diamondPriceInput.fill(prices.diamond);
        await this.lowestPriceInput.fill(prices.lowest);
        await this.platinumPriceInput.fill(prices.platinum);
        await this.goldPriceInput.fill(prices.gold);
        await this.silverPriceInput.fill(prices.silver);
    }

    async applyToAllRows() {
        // Clicking "Apply to All" for standard input (often triggers row creation or prepopulate)
        await this.applyToAllBtn.first().click();
        await this.selectAllCheckbox.check();
        await this.applyToAllBtn.last().click(); // In many grids, second click confirms for all checked
    }

    async saveChanges() {
        await this.saveBtn.click();
    }
}
