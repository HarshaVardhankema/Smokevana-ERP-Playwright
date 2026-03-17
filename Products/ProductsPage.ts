import { Page, Locator, expect } from '@playwright/test';

export class ProductsPage {
    readonly page: Page;
    readonly productNameInput: Locator;
    readonly skuInput: Locator;
    readonly ctValueInput: Locator;
    readonly brandDropdown: Locator;
    readonly productVisibilityDropdown: Locator;
    readonly categoryDropdown: Locator;
    readonly subCategoryDropdown: Locator;
    readonly alertQuantityInput: Locator;
    readonly mlValueInput: Locator;
    readonly businessLocationsDropdown: Locator;
    readonly productSourceDropdown: Locator;
    readonly unitDropdown: Locator;
    readonly barcodeTypeDropdown: Locator;
    readonly purchasePriceExcTax: Locator;
    readonly sellingPriceExcTax: Locator;
    readonly barcodeNoInput: Locator;
    readonly maxSaleLimitInput: Locator;
    readonly productTypeDropdown: Locator;
    readonly variationTemplateDropdown: Locator;
    readonly coaDescriptionInput: Locator;
    readonly storeFileInput: Locator;
    readonly saveBtn: Locator;
    readonly ecomCheckbox: Locator;
    readonly tobaccoCheckbox: Locator;
    readonly giftCardCheckbox: Locator;

    constructor(page: Page) {
        this.page = page;
        this.productNameInput = page.locator('input#name');
        this.skuInput = page.locator('input#sku');
        this.ctValueInput = page.locator('input#ct');
        this.brandDropdown = page.locator('select#brand_id');
        this.productVisibilityDropdown = page.locator('select#productVisibility');
        this.categoryDropdown = page.locator('select#category_id');
        this.subCategoryDropdown = page.locator('select#sub_category_id');
        this.alertQuantityInput = page.locator('input#alert_quantity');
        this.mlValueInput = page.locator('input#ml');
        this.businessLocationsDropdown = page.locator('select#product_locations');
        this.productSourceDropdown = page.locator('select#product_source');
        this.unitDropdown = page.locator('select#unit_id');
        this.barcodeTypeDropdown = page.locator('select#barcode_type');
        this.purchasePriceExcTax = page.locator('#single_dpp');
        this.sellingPriceExcTax = page.locator('#single_dsp');
        this.barcodeNoInput = page.locator('#barcode_no');
        this.maxSaleLimitInput = page.locator('input#maxSaleLimit');
        this.productTypeDropdown = page.locator('select#type');
        this.variationTemplateDropdown = page.locator('select[name="product_variation[0][variation_template_id]"]');
        this.coaDescriptionInput = page.locator('input[placeholder="e.g. Lab Report"]');
        this.storeFileInput = page.locator('input[type="file"]').first(); 
        
        // Buttons
        this.saveBtn = page.locator('button.submit_product_form:has-text("Save")').last(); 
        
        // Product Types (iCheck)
        this.tobaccoCheckbox = page.locator('input[name="is_tobacco_product"]');
        this.ecomCheckbox = page.locator('input[name="is_ecom_product"]');
        this.giftCardCheckbox = page.locator('input[name="is_gift_card"]');
    }

    async fillBasicInfo(data: {
        name: string,
        sku?: string,
        ct?: string,
        brand?: string,
        visibility?: string,
        category?: string,
        subCategory?: string,
        alertQty?: string,
        ml?: string,
        source?: string,
        unit?: string,
        barcodeType?: string,
        locations?: string[]
    }) {
        console.log(`LOG: Filling product name: ${data.name}`);
        await this.productNameInput.fill(data.name);
        
        if (data.sku) {
            console.log(`LOG: Filling SKU: ${data.sku}`);
            await this.skuInput.fill(data.sku);
        }
        
        if (data.brand) {
            console.log(`LOG: Selecting brand: ${data.brand}`);
            await this.select2Option(this.brandDropdown, data.brand);
        }
        
        if (data.category) {
            console.log(`LOG: Selecting category: ${data.category}`);
            await this.select2Option(this.categoryDropdown, data.category);
        }
        if (data.subCategory) await this.select2Option(this.subCategoryDropdown, data.subCategory);
        if (data.source) await this.select2Option(this.productSourceDropdown, data.source);
        if (data.visibility) await this.select2Option(this.productVisibilityDropdown, data.visibility);
        if (data.unit) await this.unitDropdown.selectOption({ label: data.unit });
        if (data.barcodeType) await this.barcodeTypeDropdown.selectOption({ label: data.barcodeType });

        if (data.alertQty) await this.alertQuantityInput.fill(data.alertQty);
        if (data.ml) await this.mlValueInput.fill(data.ml);

        if (data.locations) {
            await this.businessLocationsDropdown.selectOption(data.locations);
        }
    }

    async setAdditionalInfo(data: {
        maxSaleLimit?: string,
        ct?: string
    }) {
        if (data.maxSaleLimit) await this.maxSaleLimitInput.fill(data.maxSaleLimit);
        if (data.ct) await this.ctValueInput.fill(data.ct);
    }

    async fillPricing(purchasePrice: string, sellingPrice: string) {
        await this.purchasePriceExcTax.fill(purchasePrice);
        await this.sellingPriceExcTax.fill(sellingPrice);
    }

    async setProductType(type: 'Ecom' | 'Tobacco' | 'GiftCard') {
        const checkbox = type === 'Ecom' ? this.ecomCheckbox : (type === 'Tobacco' ? this.tobaccoCheckbox : this.giftCardCheckbox);
        await checkbox.click({ force: true });
    }

    async fillVariableInfo(data: {
        template: string,
        variations: {
            value: string,
            purchasePrice: string,
            sellingPrice: string
        }[]
    }) {
        console.log("LOG: Setting product type to Variable...");
        await this.select2Option(this.productTypeDropdown, 'Variable');
        
        console.log(`LOG: Selecting variation template: ${data.template}`);
        await this.variationTemplateDropdown.selectOption({ label: data.template });
        
        // Wait for AJAX to finish generating variation rows successfully so we don't overwrite them prematurely
        await this.page.waitForTimeout(3000);
        
        for (let i = 0; i < data.variations.length; i++) {
            const v = data.variations[i];
            console.log(`LOG: Filling variation ${i}: ${v.value}`);
            
            const valInput = this.page.locator(`input[name="product_variation[0][variations][${i}][value]"]`);
            const pPriceInput = this.page.locator(`input[name="product_variation[0][variations][${i}][default_purchase_price]"]`);
            const sPriceInput = this.page.locator(`input[name="product_variation[0][variations][${i}][default_sell_price]"]`);
            
            await valInput.fill(v.value);
            await pPriceInput.fill(v.purchasePrice);
            await sPriceInput.fill(v.sellingPrice);
        }
    }

    async uploadLabReport(description: string, filePath: string) {
        console.log(`LOG: Uploading Lab Report: ${description}`);
        await this.coaDescriptionInput.fill(description);
        await this.storeFileInput.setInputFiles(filePath);
    }

    async clickSave() {
        await this.saveBtn.click();
    }

    private async select2Option(locator: Locator, text: string) {
        try {
            await locator.selectOption({ label: text }, { timeout: 2000 });
            return;
        } catch (e) {
            const id = await locator.first().getAttribute('id');
            const container = this.page.locator(`span#select2-${id}-container, span[aria-labelledby="select2-${id}-container"]`).first();
            
            await container.click({ force: true });
            
            // Wait for dropdown to be visible
            const dropdown = this.page.locator('.select2-dropdown').first();
            await expect(dropdown).toBeVisible({ timeout: 5000 });

            const searchField = dropdown.locator('.select2-search__field');
            if (await searchField.isVisible()) {
                await searchField.fill(text);
                await this.page.waitForTimeout(1000);
            }
            
            // Try matching by role option or treeitem
            const option = this.page.locator('.select2-results__option, [role="treeitem"], [role="option"]', { hasText: new RegExp(`^${text}$`, 'i') }).first();
            if (await option.isVisible()) {
                await option.click();
            } else {
                // Fallback to fuzzy match if exact match fails
                await this.page.locator('.select2-results__option, [role="treeitem"], [role="option"]', { hasText: text }).first().click();
            }
            await this.page.waitForTimeout(500); 
        }
    }
}
