/**
 * A cache of category product counts by region
 *
 * The category product-count by region cache is used to make category
 * product count queries fast. Any updates to tables that would change
 * the available product count of a category should trigger a call to the
 * updateCategoryVisibilityProductCountRegion() function.
 *
 * This table mirrors the CatalogAvailableProductCountByRegionView view.
 *
 * The contents of this table are updated automatically through triggers.
 * See CategoryVisibleProductCountByRegionTrigger.sql for details.
 */
create table CategoryAvailableProductCountByRegionCache (
	category int not null references Category(id) on delete cascade,
	region int not null references Region(id) on delete cascade,
	product_count int not null default 0,
	primary key(category, region)
);
