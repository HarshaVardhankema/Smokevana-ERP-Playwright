import { Locator, Page } from "@playwright/test";

export class CategoriesPage {
    readonly page: Page;
    readonly productsMenu: Locator;
    readonly categoriesMenu: Locator;
    readonly addBtn: Locator;
    readonly addModal: Locator;
    
    // Add Category Form Inputs
    readonly uploadLogoBtn: Locator;
    readonly businessLocationSelect: Locator;
    readonly categoryNameInput: Locator;
    readonly slugInput: Locator;
    readonly categoryCodeInput: Locator;
    readonly descriptionInput: Locator;
    
    // Brand Selection
    readonly selectBrandsDropdown: Locator;
    readonly brandSearchInput: Locator;
    
    readonly saveBtn: Locator;

    constructor(page: Page) {
        this.page = page;
        this.productsMenu = page.getByRole('link', { name: 'Products', exact: true });
        this.categoriesMenu = page.getByRole('link', { name: 'Categories' });
        
        this.addBtn = page.getByText('Add', { exact: true });
        this.addModal = page.locator('#category_add_form');
        
        // Form Fields
        this.uploadLogoBtn = page.getByRole('button', { name: 'Upload logo:' });
        this.businessLocationSelect = page.getByLabel('Business Location:');
        this.categoryNameInput = page.getByRole('textbox', { name: 'Category name:*' });
        this.slugInput = page.getByRole('textbox', { name: 'Slug:' });
        this.categoryCodeInput = page.getByRole('textbox', { name: 'Category Code:' });
        this.descriptionInput = page.getByRole('textbox', { name: 'Description:' });
        
        // Brand selector dropdown components
        this.selectBrandsDropdown = page.getByRole('textbox', { name: 'Select brands...' });
        this.brandSearchInput = page.getByRole('searchbox').or(page.locator('input[type="search"]')); // Fallback since it opens a select2 dropdown
        
        this.saveBtn = page.getByRole('button', { name: 'Save' });
    }

    async navigate() {
        if (!await this.categoriesMenu.isVisible()) {
            await this.productsMenu.first().click({ force: true });
        }
        await this.categoriesMenu.click({ force: true });
    }

    async clickAddCategory() {
        await this.addBtn.click();
        await this.addModal.waitFor({ state: 'visible' });
    }

    async fillCategoryDetails(details: {
        logoPath?: string;
        locationValue?: string;
        name: string;
        slug: string;
        code: string;
        description: string;
        brandSearch: string;
        brandSelectName: string;
    }) {
        if (details.logoPath) {
            await this.uploadLogoBtn.click();
            await this.uploadLogoBtn.setInputFiles(details.logoPath);
        }
        
        if (details.locationValue) {
            await this.businessLocationSelect.selectOption(details.locationValue);
        }
        
        await this.categoryNameInput.click();
        await this.categoryNameInput.fill(details.name);
        
        await this.slugInput.click();
        await this.slugInput.fill(details.slug);
        
        await this.categoryCodeInput.click();
        await this.categoryCodeInput.fill(details.code);
        
        await this.descriptionInput.click();
        await this.descriptionInput.fill(details.description);
        
        // Handling the dynamic brand select dropdown
        await this.selectBrandsDropdown.click();
        await this.selectBrandsDropdown.fill(details.brandSearch);
        await this.page.getByRole('treeitem', { name: details.brandSelectName }).click();
    }

    async saveCategory() {
        await this.saveBtn.click();
    }
}
