import { Locator, Page, expect } from "@playwright/test";

export class VendorPage {
    page: Page;
    vendorCareMenu: Locator;
    vendorMenu: Locator;
    addVendorBtn: Locator;
    
    // Form Inputs
    businessNameInput: Locator;
    pleaseSelectDropdown: Locator;
    treeItemPleaseSelect: Locator;
    treeItemSmokevana: Locator;
    smokevanaCombobox: Locator;
    
    firstNameInput: Locator;
    lastNameInput: Locator;
    mobileInput: Locator;
    emailInput: Locator;
    addressLine1Input: Locator;
    addressSuggestion: Locator;
    zipCodeInput: Locator;
    
    saveBtn: Locator;
    cancelBtn: Locator;
    closeFormBtn: Locator;
    noBtn: Locator;

    constructor(page: Page) {
        this.page = page;
        
        this.vendorCareMenu = page.getByRole('link', { name: 'Vendor Care' });
        this.vendorMenu = page.getByRole('link', { name: 'Vendor', exact: true });
        
        this.addVendorBtn = page.getByRole('button', { name: 'Add' });
        this.closeFormBtn = page.locator('#contact_add_form').getByLabel('Close');
        
        this.businessNameInput = page.getByRole('textbox', { name: 'Business Name:*' });
        this.pleaseSelectDropdown = page.getByTitle('Please Select');
        this.treeItemPleaseSelect = page.getByRole('treeitem', { name: 'Please Select' });
        this.treeItemSmokevana = page.getByRole('treeitem', { name: 'Smokevana Prime B2B (BL0001)' });
        this.smokevanaCombobox = page.getByRole('combobox', { name: 'Smokevana Prime B2B (BL0001)' });
        
        this.firstNameInput = page.getByRole('textbox', { name: 'First Name:*' });
        this.lastNameInput = page.getByRole('textbox', { name: 'Last Name:*' });
        this.mobileInput = page.getByRole('textbox', { name: 'Mobile:*' });
        this.emailInput = page.getByRole('textbox', { name: 'Email:' });
        this.addressLine1Input = page.getByRole('textbox', { name: 'Address line 1:*' });
        this.addressSuggestion = page.getByText('Ohio 39Millersburg, OH, USA');
        this.zipCodeInput = page.getByRole('textbox', { name: 'Zip Code:*' });
        
        this.saveBtn = page.getByRole('button', { name: 'Save' });
        this.cancelBtn = page.getByRole('button', { name: 'Cancel' });
        this.noBtn = page.getByRole('button', { name: 'No' });
    }

    async navigateToVendor() {
        await this.vendorCareMenu.click();
        await this.vendorMenu.click();
        await expect(this.page.getByRole('row', { name: 'Action Supplier ID: activate' }).first()).toBeVisible();
    }

    async clickAddVendor() {
        await this.addVendorBtn.click();
        await expect(this.closeFormBtn).toBeVisible();
    }
    
    async fillVendorDetails(details: { businessName: string; firstName: string; lastName: string; mobile: string; email: string; address1: string; zipCode: string; }) {
        await this.businessNameInput.click();
        await this.businessNameInput.fill(details.businessName);
        
        await this.pleaseSelectDropdown.click();
        await expect(this.treeItemPleaseSelect).toBeVisible();
        
        await this.treeItemSmokevana.click();
        await expect(this.smokevanaCombobox).toBeVisible();
        
        await this.firstNameInput.click();
        await this.firstNameInput.fill(details.firstName);
        
        await this.lastNameInput.click();
        await this.lastNameInput.fill(details.lastName);
        
        await this.mobileInput.click();
        await this.mobileInput.fill(details.mobile);
        
        await this.emailInput.click();
        await this.emailInput.fill(details.email);
        
        await this.addressLine1Input.click();
        await this.addressLine1Input.fill(details.address1);
        await this.addressSuggestion.click();
        
        await this.zipCodeInput.click();
        await this.zipCodeInput.fill(details.zipCode);
    }
    
    async updateMobile(mobile: string) {
        await this.mobileInput.click();
        await this.mobileInput.fill(mobile);
    }

    async saveVendor() {
        await this.saveBtn.click();
    }
    
    async clickCancel() {
        await this.cancelBtn.click();
    }
    
    async clickNo() {
        await this.noBtn.click();
    }
}
