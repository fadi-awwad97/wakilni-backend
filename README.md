# API Routes Documentation

This document provides an overview of the available API routes and their corresponding functionalities.

## Authentication Routes

### User Login

- **Route:** `POST /login`
- **Controller:** `AuthController@login`
- **Description:** Allows users to log in by providing valid credentials.

### User Signup

- **Route:** `POST /signup`
- **Controller:** `AuthController@signup`
- **Description:** Allows users to create a new account by providing necessary registration details.

## Authenticated Routes

The following routes require authentication via an API token.

### Product Routes

#### Create a New Product

- **Route:** `POST /products/create`
- **Controller:** `ProductController@store`
- **Description:** Allows authenticated users to create a new product.

#### List Products

- **Route:** `GET /products`
- **Controller:** `ProductController@list`
- **Description:** Retrieves a list of products.

#### List Items for a Product

- **Route:** `GET /items/{product_id}`
- **Controller:** `ProductController@list_items`
- **Description:** Retrieves a list of items associated with a specific product.

#### Update Product

- **Route:** `POST /update-product/{id}`
- **Controller:** `ProductController@update`
- **Description:** Allows authenticated users to update an existing product by its ID.

#### Delete Product

- **Route:** `DELETE /delete-product/{id}`
- **Controller:** `ProductController@destroy`
- **Description:** Allows authenticated users to delete a product by its ID.

### Item Routes

#### Add Item to Product

- **Route:** `POST /add-item/{id}`
- **Controller:** `ProductController@addItem`
- **Description:** Allows authenticated users to add a new item to a specific product.

#### Update Item

- **Route:** `POST /update-item/{id}`
- **Controller:** `ProductController@updateItem`
- **Description:** Allows authenticated users to update an existing item by its ID.

#### Delete Item

- **Route:** `DELETE /delete-item/{id}`
- **Controller:** `ProductController@deleteItem`
- **Description:** Allows authenticated users to delete an item by its ID.

#### Mark Item as Sold

- **Route:** `POST /sold-item/{id}`
- **Controller:** `ProductController@updateSoldStatus`
- **Description:** Allows authenticated users to mark an item as sold by its ID.

### User Logout

- **Route:** `POST /logout`
- **Controller:** `AuthController@logout`
- **Description:** Allows users to log out of their authenticated session by invalidating the API token.

Please note that some routes require specific parameters or request payloads, and authentication is required for accessing most of the routes within the authenticated group. Ensure that you include the necessary data and use the appropriate HTTP methods when interacting with these routes.
