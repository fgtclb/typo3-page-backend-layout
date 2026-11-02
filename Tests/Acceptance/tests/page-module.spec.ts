import { expect, FrameLocator, Page, test } from '@playwright/test';

class Backend {
  constructor(private readonly page: Page) {}

  async loginAsAdmin(): Promise<void> {
    await this.page.goto('/typo3/');
    await this.page.locator('#t3-username').fill('admin');
    await this.page.locator('#t3-password').fill(process.env.TYPO3_ACCEPTANCE_ADMIN_PASSWORD ?? '');
    await this.page.locator('#t3-login-submit').click();
    await expect(this.page.locator('#modulemenu')).toBeVisible();
  }

  async openPageModule(pageId: number): Promise<FrameLocator> {
    await this.page.goto(`/typo3/module/web/layout?id=${pageId}`);
    const contentFrame = this.page.frameLocator('#typo3-contentIframe');
    await expect(contentFrame.locator('.module-body')).toBeVisible();
    return contentFrame;
  }
}

test.describe('page module', () => {
  test.beforeEach(async ({ page }) => {
    await new Backend(page).loginAsAdmin();
  });

  test('renders the partial of the page type with the page media', async ({ page }) => {
    const module = await new Backend(page).openPageModule(1);

    await expect(module.getByTestId('page-backend-layout-title')).toHaveText('Page backend layout of Page with media');
    await expect(module.getByTestId('page-backend-layout-media').locator('li')).toHaveText(['teaser.jpg']);
  });

  test('renders the partial of the page type without page media', async ({ page }) => {
    const module = await new Backend(page).openPageModule(2);

    await expect(module.getByTestId('page-backend-layout-title')).toHaveText('Page backend layout of Page without media');
    await expect(module.getByTestId('page-backend-layout-media').locator('li')).toHaveCount(0);
  });

  test('keeps the content grid of the core', async ({ page }) => {
    const module = await new Backend(page).openPageModule(2);

    await expect(module.getByRole('button', { name: 'Create new content' })).toBeVisible();
  });

  test('renders no partial for a page type without one', async ({ page }) => {
    const module = await new Backend(page).openPageModule(3);

    await expect(module.getByRole('heading', { name: 'Folder' })).toBeVisible();
    await expect(module.getByTestId('page-backend-layout-doktype-1')).toHaveCount(0);
  });
});
