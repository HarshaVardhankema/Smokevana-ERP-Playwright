import { Page, BrowserContext } from "@playwright/test";
import { SalesCreatePage } from "./Salescreatepage";
import { SalesOrderPage } from "./Salesorderpage";
import { SalesInvoicePage } from "./Salesinvoicepage";
import { ListReturnPage } from "./Listreturnpage";
import { NewSalesorderPage } from "./NewSalesorderpage";

export class SalesDataManager {
    readonly page: Page;
    readonly context: BrowserContext;
    private salesCreatePage: SalesCreatePage;
    private salesOrderPage: SalesOrderPage;
    private salesInvoicePage: SalesInvoicePage;
    private listReturnPage: ListReturnPage;
    private newSalesorderPage: NewSalesorderPage;

    constructor(page: Page, context: BrowserContext) {
        this.page = page;
        this.context = context;
        this.salesCreatePage = new SalesCreatePage(this.page);
        this.salesOrderPage = new SalesOrderPage(page);
        this.salesInvoicePage = new SalesInvoicePage(page);
        this.listReturnPage = new ListReturnPage(page);
        this.newSalesorderPage = new NewSalesorderPage(page);
    }

    getSalesCreatePage(): SalesCreatePage {
        return this.salesCreatePage;
    }

    getSalesOrderPage(): SalesOrderPage {
        return this.salesOrderPage;
    }

    getSalesInvoicePage(): SalesInvoicePage {
        return this.salesInvoicePage;
    }

    getListReturnPage(): ListReturnPage {
        return this.listReturnPage;
    }

    getNewSalesorderPage(): NewSalesorderPage {
        return this.newSalesorderPage;
    }
}

