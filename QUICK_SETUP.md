# 🚀 Quick Setup Script for Food Ordering System

## 📋 Prerequisites Check
Make sure you have the following installed:
- ✅ Node.js 18+ 
- ✅ PHP 8.2+
- ✅ Composer
- ✅ MySQL 8.0+
- ✅ Git

## 🛠️ One-Command Setup

### **Windows PowerShell:**
```powershell
# Navigate to project directory
cd "B:\Beltie University\YEAR3-SE\System Analysis Design\Project_Ass\food-ordering-system"

# Install dependencies
composer install && npm install

# Setup environment
if (!(Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate

# Database setup (make sure MySQL is running)
php artisan migrate --seed

# Start development servers
Start-Process powershell -ArgumentList "-Command", "npm run dev"
Start-Process powershell -ArgumentList "-Command", "php artisan serve"
```

### **Linux/macOS:**
```bash
# Navigate to project directory
cd "/path/to/food-ordering-system"

# Install dependencies
composer install && npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Start development servers (in separate terminals)
npm run dev &
php artisan serve &
```

## 🌐 Access Your Application

After running the setup:
- **Frontend Dev Server**: http://localhost:5173
- **Laravel Server**: http://localhost:8000
- **Full Application**: http://localhost:8000

## 🔑 Demo Accounts

The seeder creates these demo accounts:
- **Customer**: customer@test.com / password
- **Manager**: manager@test.com / password  
- **Kitchen**: kitchen@test.com / password

## 🎯 Quick Start Features

1. **Visit http://localhost:8000** - See the welcome page
2. **Login with demo account** - Access dashboard
3. **Browse Products** - View the menu
4. **Place Orders** - Test ordering system
5. **Manage Inventory** - (Manager role)
6. **View Reports** - (Manager role)

## 🚨 Troubleshooting

### **Port Already in Use:**
```bash
# Kill processes on ports 5173 and 8000
npx kill-port 5173
npx kill-port 8000
```

### **Database Connection Issues:**
```bash
# Check MySQL is running
# Update .env with correct database credentials
php artisan config:clear
```

### **Permission Issues:**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

### **Node Modules Issues:**
```bash
# Clean install
rm -rf node_modules package-lock.json
npm install
```

## 📱 Mobile Testing

Test responsive design:
1. Open browser dev tools (F12)
2. Toggle device toolbar
3. Test on mobile viewport
4. Check navigation drawer functionality

## 🎨 Vuetify Components Available

Your project includes these Vuetify components:
- ✅ **Layout**: v-app, v-app-bar, v-navigation-drawer
- ✅ **Forms**: v-text-field, v-select, v-btn, v-form
- ✅ **Data**: v-data-table, v-card, v-list
- ✅ **Feedback**: v-snackbar, v-alert, v-dialog
- ✅ **Navigation**: v-menu, v-tabs, v-breadcrumbs

## 🔧 Development Workflow

1. **Make changes** to Vue components in `resources/js/Pages/`
2. **Hot reload** automatically updates the browser
3. **Test on different screen sizes**
4. **Commit changes** to Git
5. **Deploy** when ready

## 📚 Next Steps

1. **Customize the theme** in `resources/js/app.js`
2. **Add new pages** following the existing structure
3. **Implement business logic** in Laravel controllers
4. **Add more Vuetify components** as needed
5. **Test thoroughly** before deployment

---

**Ready to start coding! 🚀**
