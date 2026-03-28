import { Page, BrowserContext } from "@playwright/test";
import { LoginPage } from "../UserManagement/Loginpage";
import { ProductsPage } from "./ProductsPage";
import { EditSellingPricePage } from "./EditSellingPricePage";
import { CategoriesPage } from "./CategoriesPage";
import { BrandsPage } from "./BrandsPage";

export class ProductsDataManager {
    readonly page: Page;
    readonly context: BrowserContext;
    private loginPage: LoginPage;
    private productsPage: ProductsPage;
    private editSellingPricePage: EditSellingPricePage;
    private categoriesPage: CategoriesPage;
    private brandsPage: BrandsPage;

    constructor(page: Page, context: BrowserContext) {
        this.page = page;
        this.context = context;

        this.loginPage = new LoginPage(this.page);
        this.productsPage = new ProductsPage(this.page);
        this.editSellingPricePage = new EditSellingPricePage(this.page);
        this.categoriesPage = new CategoriesPage(this.page);
        this.brandsPage = new BrandsPage(this.page);
    }

    getLoginPage(): LoginPage {
        return this.loginPage;
    }

    getProductsPage(): ProductsPage {
        return this.productsPage;
    }

    getEditSellingPricePage(): EditSellingPricePage {
        return this.editSellingPricePage;
    }

    getCategoriesPage(): CategoriesPage {
        return this.categoriesPage;
    }

    getBrandsPage(): BrandsPage {
        return this.brandsPage;
    }
}
