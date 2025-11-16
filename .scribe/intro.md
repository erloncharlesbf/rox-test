# Introduction

Complete API documentation for managing Books and Movies with CRUD operations, file uploads, and advanced filtering capabilities.

<aside>
    <strong>Base URL</strong>: <code>https://rox-test.test</code>
</aside>

    <p>Welcome to the <strong>Books &amp; Movies API</strong> documentation! This RESTful API provides a comprehensive solution for managing digital libraries and movie collections.</p>

    <h2>Features</h2>
    <ul>
        <li><strong>Complete CRUD Operations</strong>: Create, read, update, and delete books and movies</li>
        <li><strong>File Upload Support</strong>: Attach cover images to your books and movies using polymorphic relationships</li>
        <li><strong>Advanced Filtering</strong>: Filter results by multiple criteria (title, author, genre, status, etc.)</li>
        <li><strong>Pagination</strong>: Efficient data retrieval with customizable pagination</li>
        <li><strong>Validation</strong>: Robust data validation with detailed error messages</li>
        <li><strong>Soft Deletes</strong>: Records are safely archived instead of permanently deleted</li>
    </ul>

    <h2>Resources</h2>
    <p>This API manages two main resources:</p>
    <ul>
        <li><strong>Books</strong>: Complete book information including title, author, ISBN, publisher, and more</li>
        <li><strong>Movies</strong>: Comprehensive movie details including director, studio, rating, duration, and more</li>
    </ul>

    <h2>Getting Started</h2>
    <p>All endpoints are prefixed with <code>/api</code>. Responses are returned in JSON format. Browse the endpoints below to see detailed information about each operation.</p>

    <aside class="notice">
        <p>💡 <strong>Tip:</strong> Use the code examples on the right to see how to interact with the API in different programming languages. You can switch between languages using the tabs at the top right.</p>
    </aside>

    <aside class="warning">
        <p>⚠️ <strong>Note:</strong> Some operations require file uploads (e.g., cover images). Make sure to use <code>multipart/form-data</code> content type for these requests.</p>
    </aside>

