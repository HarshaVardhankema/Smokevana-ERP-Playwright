import { Locator, Page } from "@playwright/test";

export class BrandsPage {
    readonly page: Page;
    readonly productsMenu: Locator;
    readonly brandsMenu: Locator;
    
    readonly addBrandBtn: Locator;
    
    // Add Brand Form Inputs
    readonly businessLocationSelect: Locator;
    readonly brandNameInput: Locator;
    readonly shortDescriptionInput: Locator;
    readonly saveBrandBtn: Locator;

    constructor(page: Page) {
        this.page = page;
        
        // Navigation Options
        this.productsMenu = page.getByRole('link', { name: 'Products', exact: true });
        this.brandsMenu = page.getByRole('link', { name: 'Brands' });
        
        // Creating Brand
        this.addBrandBtn = page.getByText('Add Brand', { exact: true });
        this.businessLocationSelect = page.getByLabel('Business Location:');
        this.brandNameInput = page.getByRole('textbox', { name: 'Brand name:*' });
        this.shortDescriptionInput = page.getByRole('textbox', { name: 'Short description:' });
        this.saveBrandBtn = page.getByRole('button', { name: ' Save Brand' });
    }

    async navigate() {
        if (!await this.brandsMenu.isVisible()) {
            await this.productsMenu.first().click({ force: true });
        }
        await this.brandsMenu.click({ force: true });
    }

    async clickAddBrand() {
        await this.addBrandBtn.click();
    }

    async fillBrandDetails(details: { locationValue: string; name: string; description: string }) {
        await this.businessLocationSelect.selectOption(details.locationValue);
        
        await this.brandNameInput.click();
        await this.brandNameInput.fill(details.name);
        
        await this.shortDescriptionInput.click();
        await this.shortDescriptionInput.fill(details.description);
    }

    async saveBrand() {
        await this.saveBrandBtn.click();
    }
}
