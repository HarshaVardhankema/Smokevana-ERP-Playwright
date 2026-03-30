import { Page, BrowserContext } from "@playwright/test";
import { LoginPage } from "../UserManagement/Loginpage";
import { VendorPage } from "./vendor";
import { PurchaseOrderPage } from "./PurchaseOrderPage";
import { ListPurchaseReturnPage } from "./ListPurchaseReturnPage";
import { ListPurchaseRequestPage } from "./ListPurchaseRequestPage";

export class VendorDataManager {
    page: Page;
    context: BrowserContext;
    private loginPage: LoginPage;
    private vendorPage: VendorPage;
    private purchaseOrderPage: PurchaseOrderPage;
    private listPurchaseReturnPage: ListPurchaseReturnPage;
    private listPurchaseRequestPage: ListPurchaseRequestPage;

    constructor(page: Page, context: BrowserContext) {
        this.page = page;
        this.context = context;

        this.loginPage = new LoginPage(this.page);
        this.vendorPage = new VendorPage(this.page);
        this.purchaseOrderPage = new PurchaseOrderPage(this.page);
        this.listPurchaseReturnPage = new ListPurchaseReturnPage(this.page);
        this.listPurchaseRequestPage = new ListPurchaseRequestPage(this.page);
    }

    getLoginPage(): LoginPage {
        return this.loginPage;
    }

    getVendorPage(): VendorPage {
        return this.vendorPage;
    }

    getPurchaseOrderPage(): PurchaseOrderPage {
        return this.purchaseOrderPage;
    }

    getListPurchaseReturnPage(): ListPurchaseReturnPage {
        return this.listPurchaseReturnPage;
    }

    getListPurchaseRequestPage(): ListPurchaseRequestPage {
        return this.listPurchaseRequestPage;
    }
}
