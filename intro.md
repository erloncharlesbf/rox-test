# Introduction

Complete API documentation for managing Books and Movies with CRUD operations, file uploads, and advanced filtering capabilities.

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    Welcome to the **Books & Movies API** documentation! This RESTful API provides a comprehensive solution for managing digital libraries and movie collections.

    ## 🚀 Features

    - **Complete CRUD Operations**: Create, read, update, and delete books and movies
    - **File Upload Support**: Attach cover images to your books and movies using polymorphic relationships
    - **Advanced Filtering**: Filter results by multiple criteria (title, author, genre, status, etc.)
    - **Pagination**: Efficient data retrieval with customizable pagination
    - **Validation**: Robust data validation with detailed error messages
    - **Soft Deletes**: Records are safely archived instead of permanently deleted

    ## 📚 Resources

    This API manages two main resources:
    - **Books**: Complete book information including title, author, ISBN, publisher, and more
    - **Movies**: Comprehensive movie details including director, studio, rating, duration, and more

    ## 🔍 Getting Started

    All endpoints are prefixed with `/api`. Responses are returned in JSON format. Browse the endpoints below to see detailed information about each operation.

    <aside class="notice">
    💡 <strong>Tip:</strong> Use the code examples on the right to see how to interact with the API in different programming languages. You can switch between languages using the tabs at the top right.
    </aside>

    <aside class="warning">
    ⚠️ <strong>Note:</strong> Some operations require file uploads (e.g., cover images). Make sure to use <code>multipart/form-data</code> content type for these requests.
    </aside>

