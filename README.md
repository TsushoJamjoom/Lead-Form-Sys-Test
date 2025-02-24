## How to Run the Project

Follow these steps to set up the project on your local machine:

### 1. Clone the Repository
```bash
git clone https://github.com/TsushoJamjoom/Leads-form.git
```
Alternatively, you can manually download the project as a ZIP file and extract it.

### 2. Install Dependencies
Run the following command to install PHP dependencies:
```bash
composer install
```

Run the following command to install Node.js dependencies:
```bash
npm install
```

### 3. Build Frontend Assets
```bash
npm run dev
```

### 4. Configure Environment Variables
Create a copy of the `.env.example` file:
```bash
cp .env.example .env
```

### 5. Create a Testing Database Configuration in Laravel

Open .env and update DB_DATABASE with your testing database name. For example:
```bash
DB_DATABASE=testing
```
Open .env and update DB_DATABASE with your testing database name.

### 6. Generate Application Key
```bash
php artisan key:generate
```

### 7. Start the Development Server
```bash
php artisan serve
```

Your Laravel application should now be running. Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser to access it.
