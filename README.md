# Strata Management System

A secure web-based strata property management system built with PHP and MySQL.

## 🔒 Security Features

This system implements enterprise-grade security measures:

### Authentication & Session Security
- **Secure Session Configuration**: HTTP-only, secure, SameSite cookies
- **Session Timeout**: 30-minute automatic timeout with activity tracking
- **Session Regeneration**: Prevents session fixation attacks
- **Strong Password Requirements**: Minimum 8 characters with complexity requirements
- **Rate Limiting**: Prevents brute force attacks (5 attempts per 15 minutes)
- **CSRF Protection**: All forms protected with secure CSRF tokens

### Role-Based Access Control (RBAC)
- **Three User Roles**: Owner, Committee, Admin
- **Granular Permissions**: Different access levels for different features
- **Secure Role Assignment**: Admin roles can only be created by existing admins
- **Access Control Functions**: `requireRole()`, `hasRole()`, `hasAnyRole()`

### Password Security
- **Argon2ID Hashing**: Industry-standard password hashing algorithm
- **High-Cost Parameters**: Memory-hard hashing with strong parameters
- **Password Complexity**: Enforced uppercase, lowercase, and numeric requirements

### Input Validation & Sanitization
- **XSS Prevention**: All user input properly sanitized and escaped
- **SQL Injection Protection**: Prepared statements throughout
- **Email Validation**: Proper email format validation
- **Input Length Limits**: Prevents buffer overflow attacks

## 🚀 Getting Started

### Prerequisites
- PHP 8.0 or higher
- MySQL/PostgreSQL database
- Web server (Apache/Nginx) or Vercel for deployment

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd strata-advanced
   ```

2. **Set up environment variables**
   Create environment variables for database connection:
   ```
   DB_HOST=your_database_host
   DB_NAME=strata_db
   DB_USER=your_database_user
   DB_PASS=your_database_password
   DB_PORT=3306
   ```

3. **Initialize database**
   Run the SQL schema:
   ```bash
   mysql -u your_user -p strata_db < api/database/schema.sql
   ```

4. **Create first admin account**
   Visit `/api/admin/create_admin.php` to create the initial admin account.

### User Roles & Permissions

#### 🔴 Admin
- Full system access
- Can create additional admin accounts
- View all user details and system information
- Access to all features and data

#### 🟡 Committee
- Manage property operations
- View owner contact information (but not emails)
- Access maintenance requests and notices
- Limited administrative functions

#### 🔵 Owner
- Basic property owner access
- View documents and notices
- Submit maintenance requests
- Access owners directory (limited view)

## 📱 Features

### For All Users
- **Secure Authentication**: Login/logout with session management
- **Dashboard**: Personalized dashboard based on role
- **Password Security**: Strong password requirements

### For Owners
- View important notices and updates
- Access owners directory
- Submit maintenance requests
- View documents and pay levies

### For Committee Members
- All owner features plus:
- Access to owner contact information
- Enhanced property management tools

### For Administrators
- All features plus:
- User management
- System administration
- Full access to all data and reports

## 🛡️ Security Improvements Made

### Critical Fixes Applied:

1. **Fixed Registration Vulnerability**
   - ❌ **Before**: Anyone could register as admin
   - ✅ **After**: Admin accounts can only be created by existing admins

2. **Added CSRF Protection**
   - ❌ **Before**: Forms vulnerable to CSRF attacks
   - ✅ **After**: All forms protected with secure CSRF tokens

3. **Implemented Rate Limiting**
   - ❌ **Before**: No protection against brute force
   - ✅ **After**: 5 login attempts per 15-minute window

4. **Enhanced Session Security**
   - ❌ **Before**: Basic session handling
   - ✅ **After**: Secure cookies, timeout, regeneration

5. **Fixed Access Control**
   - ❌ **Before**: Inconsistent role checking
   - ✅ **After**: Proper RBAC with granular permissions

6. **Secured API Endpoints**
   - ❌ **Before**: No authentication required
   - ✅ **After**: All endpoints require proper authentication

7. **Improved Password Security**
   - ❌ **Before**: Basic password hashing
   - ✅ **After**: Argon2ID with high-cost parameters

## 🔧 API Endpoints

### Authentication
- `POST /api/pages/login.php` - User login
- `POST /api/pages/register.php` - User registration (owner/committee only)
- `GET /api/logout.php` - User logout
- `POST /api/admin/create_admin.php` - Admin account creation

### Protected Endpoints
- `GET /api/notices.php` - Important notices (requires login)
- `GET /api/updates.php` - Recent updates (requires login)
- `GET /api/pages/owners.php` - Owners directory (role-based access)

## 🚨 Security Best Practices

### For Developers
1. Always use prepared statements for database queries
2. Validate and sanitize all user input
3. Implement proper error handling without exposing sensitive information
4. Use HTTPS in production environments
5. Regularly update dependencies

### For Administrators
1. Use strong, unique passwords
2. Limit admin account creation
3. Monitor login attempts and sessions
4. Regular security audits
5. Keep system updated

### For Users
1. Use strong passwords with complexity requirements
2. Don't share account credentials
3. Log out when finished
4. Report suspicious activity

## 📊 Database Schema

The system uses the following main tables:
- `users` - User accounts and roles
- `units` - Property unit information
- `notices` - Important notices and announcements
- `updates` - System updates and news
- `maintenance_requests` - Maintenance requests
- `documents` - Document management
- `levies` - Levy information

## 🔍 Monitoring & Logging

The system logs important security events:
- Failed login attempts
- Authentication errors
- Database connection issues
- Access control violations

## 📞 Support

For security issues or vulnerabilities, please report them responsibly to the development team.

---

**Security Status**: ✅ **Secure** - All major vulnerabilities have been identified and fixed. 