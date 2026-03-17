import { Page, BrowserContext } from "@playwright/test";
import { CustomersPage } from "./CustomersPage";
import { CustomerGroupsPage } from "./CustomerGroupsPage";

export class CustomerCareDataManager {
    readonly page: Page;
    readonly context: BrowserContext;
    private customersPage: CustomersPage;
    private customerGroupsPage: CustomerGroupsPage;

    constructor(page: Page, context: BrowserContext) {
        this.page = page;
        this.context = context;
        this.customersPage = new CustomersPage(page);
        this.customerGroupsPage = new CustomerGroupsPage(page);
    }

    getCustomersPage(): CustomersPage {
        return this.customersPage;
    }

    getCustomerGroupsPage(): CustomerGroupsPage {
        return this.customerGroupsPage;
    }
}
