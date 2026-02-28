# WooCommerce to ERP Product Sync

This document explains how to use the new `syncProductFromWooToErp` function that synchronizes products from WooCommerce to the ERP system.

## Overview

The new sync function is designed to handle large-scale product synchronization (20,000+ products) with multiple variations efficiently. It fetches products from WooCommerce in chunks and creates or updates them in the ERP system.

## Features

- **Chunked Processing**: Processes products in configurable chunks (default 100) to handle large datasets
- **Incremental Sync**: Supports syncing only updated products since last sync
- **Multiple Variations**: Handles both simple and variable products with their variations
- **Error Handling**: Comprehensive error handling and logging
- **Memory Efficient**: Uses pagination to prevent memory issues with large datasets
- **Category Mapping**: Automatically creates and maps categories between systems

## Usage

### Basic Usage

```php
// Sync all products (first 100)
$result = $woocommerceController->syncProductFromWooToErp();

// Sync with custom parameters
$result = $woocommerceController->syncProductFromWooToErp([
    'limit' => 200,
    'offset' => 0,
    'sync_type' => 'all' // all, new, updated
]);
```

### Parameters

- `limit` (int): Number of products to process per request (default: 100)
- `offset` (int): Starting position for pagination (default: 0)
- `sync_type` (string): Type of sync to perform
  - `all`: Sync all products
  - `new`: Sync only new products
  - `updated`: Sync only products modified since last sync

### Response Format

```php
[
    'success' => 1,
    'msg' => '100 products synced successfully',
    'total_products' => 100,
    'created_products' => ['Product 1', 'Product 2'],
    'updated_products' => ['Product 3', 'Product 4'],
    'skipped_products' => ['Product 5'],
    'has_more' => true,
    'next_offset' => 100
]
```

## Implementation Details

### Main Function: `syncProductsFromWooToErp`

Located in `Modules/Woocommerce/Utils/WoocommerceUtil.php`, this function:

1. **Fetches Products**: Uses WooCommerce REST API to get products in chunks
2. **Processes Each Product**: Determines if product exists and creates/updates accordingly
3. **Handles Variations**: Creates product variations for variable products
4. **Maps Categories**: Finds or creates categories in ERP system
5. **Logs Activity**: Creates detailed sync logs for tracking

### Product Processing

#### New Products
- Creates new product record in ERP
- Maps WooCommerce product ID for future syncs
- Creates variations (single or multiple)
- Assigns categories
- Sets default values for required fields

#### Existing Products
- Updates product information (name, description, etc.)
- Updates variation prices
- Maintains existing relationships

### Variation Handling

#### Simple Products
- Creates single "DUMMY" variation
- Maps WooCommerce product ID to variation

#### Variable Products
- Creates variation template (default: "Size")
- Creates product variation
- Creates individual variations for each WooCommerce variation
- Maps WooCommerce variation IDs

## Error Handling

The function includes comprehensive error handling:

- **API Errors**: Catches and logs WooCommerce API errors
- **Database Errors**: Handles database transaction failures
- **Individual Product Errors**: Continues processing even if individual products fail
- **Detailed Logging**: Logs all activities and errors for debugging

## Performance Considerations

### Memory Management
- Processes products in chunks to prevent memory issues
- Uses pagination for large datasets
- Clears memory after each chunk

### API Rate Limiting
- Respects WooCommerce API rate limits
- Uses efficient batch operations where possible

### Database Optimization
- Uses database transactions for data integrity
- Minimizes database queries through efficient relationships

## Configuration

### Required Settings
- WooCommerce API credentials (consumer key/secret)
- WooCommerce site URL
- Business ID for ERP system

### Optional Settings
- Default unit for products
- Default tax settings
- Category mapping preferences

## Monitoring

### Sync Logs
All sync activities are logged in the `woocommerce_sync_logs` table with:
- Sync type and operation
- Number of products processed
- Success/failure status
- Error details if any

### Progress Tracking
The function returns progress information:
- Total products processed
- Number of created/updated/skipped products
- Whether more products are available
- Next offset for pagination

## Example Usage Scenarios

### Initial Sync (20,000 products)
```php
$offset = 0;
$limit = 100;
$total_processed = 0;

do {
    $result = $woocommerceController->syncProductFromWooToErp([
        'limit' => $limit,
        'offset' => $offset
    ]);
    
    $total_processed += $result['total_products'];
    $offset = $result['next_offset'];
    
    // Log progress
    echo "Processed: $total_processed products\n";
    
} while ($result['has_more']);
```

### Incremental Sync (Updated products only)
```php
$result = $woocommerceController->syncProductFromWooToErp([
    'sync_type' => 'updated',
    'limit' => 200
]);
```

## Troubleshooting

### Common Issues

1. **API Connection Errors**
   - Verify WooCommerce API credentials
   - Check WooCommerce site URL
   - Ensure API is enabled in WooCommerce

2. **Memory Issues**
   - Reduce chunk size (limit parameter)
   - Increase PHP memory limit
   - Process in smaller batches

3. **Database Errors**
   - Check database permissions
   - Verify required tables exist
   - Review transaction logs

### Debug Information
- Check Laravel logs for detailed error messages
- Review sync logs in database
- Monitor WooCommerce API response times

## Future Enhancements

Potential improvements for future versions:
- Webhook support for real-time sync
- Advanced category mapping
- Image synchronization
- Stock level synchronization
- Custom field mapping
- Bulk operation optimization 