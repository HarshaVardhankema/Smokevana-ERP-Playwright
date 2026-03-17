import { Page, Locator } from "@playwright/test";

export class DashboardPage {
    readonly page: Page;
    readonly userManagementMenu: Locator;
    readonly contactsMenu: Locator;
    readonly productsMenu: Locator;
    readonly purchasesMenu: Locator;
    readonly saleMenu: Locator;
    readonly salesOrderSubMenu: Locator;
    readonly salesInvoiceMenu: Locator;
    readonly addSalesInvoiceMenu: Locator;
    readonly listSellReturnMenu: Locator;
    readonly orderFulfillmentMenu: Locator;
    readonly customersSubMenu: Locator;
    readonly customerGroupsSubMenu: Locator;
    readonly importContactsSubMenu: Locator;
    readonly usersSubMenu: Locator;
    readonly userProfileDropdown: Locator;
    readonly logoutButton: Locator;

    constructor(page: Page) {
        this.page = page;
        // Sidebar Locators based on AdminSidebarMenu.php labels
        this.userManagementMenu = page.getByRole('link', { name: 'User Management' });
        this.contactsMenu = page.getByRole('link', { name: 'Customer Care' });
        this.productsMenu = page.getByRole('link', { name: 'Products' });
        this.purchasesMenu = page.getByRole('link', { name: 'Vendor Care' });
        this.saleMenu = page.locator('#tour_step7');
        this.salesOrderSubMenu = page.getByRole('link', { name: 'Sales Order (SO)' });
        this.salesInvoiceMenu = page.getByRole('link', { name: 'Sales Invoice (SI)' });
        this.addSalesInvoiceMenu = page.getByRole('link', { name: 'Add Sale Invoice (SI)' });
        this.listSellReturnMenu = page.getByRole('link', { name: 'List Sell Return(CN)' });
        this.orderFulfillmentMenu = page.getByRole('link', { name: 'Order Fulfillment' });
        this.customersSubMenu = page.getByRole('link', { name: 'Customers', exact: true });
        this.customerGroupsSubMenu = page.getByRole('link', { name: 'Customer Groups' });
        this.importContactsSubMenu = page.getByRole('link', { name: 'Import Contacts' });
        this.usersSubMenu = page.getByRole('link', { name: 'Users' });

        // Header Locators
        this.userProfileDropdown = page.locator('li.user-menu a.dropdown-toggle');
        this.logoutButton = page.locator('a:has-text("Sign Out")'); // Common label or Sign Out based on controllers
    }

    async clickSale() {
        // Ensure the menu is visible and click it to expand
        await this.saleMenu.waitFor({ state: 'visible' });
        await this.saleMenu.click();
    }

    async clickSalesOrder() {
        // Ensure the sub-menu is visible, if not, click the parent again
        const isVisible = await this.salesOrderSubMenu.isVisible();
        if (!isVisible) {
            await this.saleMenu.click();
        }
        await this.salesOrderSubMenu.waitFor({ state: 'visible' });
        await this.salesOrderSubMenu.click();
    }

    async clickAddSalesInvoice() {
        const isVisible = await this.addSalesInvoiceMenu.isVisible();
        if (!isVisible) {
            await this.saleMenu.click();
        }
        await this.addSalesInvoiceMenu.waitFor({ state: 'visible' });
        await this.addSalesInvoiceMenu.click();
    }

    async clickListSellReturn() {
        const isVisible = await this.listSellReturnMenu.isVisible();
        if (!isVisible) {
            await this.saleMenu.click();
        }
        await this.listSellReturnMenu.waitFor({ state: 'visible' });
        await this.listSellReturnMenu.click();
    }

    async clickUserManagement() {
        await this.userManagementMenu.click();
    }

    async clickUsers() {
        await this.usersSubMenu.first().click();
    }


    async clickContacts() {
        await this.contactsMenu.click();
    }

    async clickCustomers() {
        if (!await this.customersSubMenu.isVisible()) {
            await this.clickContacts();
        }
        await this.customersSubMenu.click();
    }

    async clickCustomerGroups() {
        if (!await this.customerGroupsSubMenu.isVisible()) {
            await this.clickContacts();
        }
        await this.customerGroupsSubMenu.click();
    }

    async clickImportContacts() {
        if (!await this.importContactsSubMenu.isVisible()) {
            await this.clickContacts();
        }
        await this.importContactsSubMenu.click();
    }

    async clickProducts() {
        await this.productsMenu.click();
    }

    async clickOrderFulfillment() {
        await this.orderFulfillmentMenu.click();
    }

    async logout() {
        await this.userProfileDropdown.click();
        await this.logoutButton.click();
    }
}
