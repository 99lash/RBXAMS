# Roblox Assets Monitoring System (RBXAMS)

The Asset Monitoring System is a specialized tool designed to track revenue and loss generated through Robux sales on RBXCRATE. It monitors both types of account (Pending and Fastflip) providing real-time insights into cash flow and transaction statuses. The system automatically updates Pending account statuses based on their Unpend (Release) Date, which supports faster reselling and reinvestment. By streamlining revenue tracking and improving operational efficiency, the system allows users to monitor profits and manage Robux transactions more effectively.

### Table of Contents

- [Roblox Assets Monitoring System (RBXAMS)](#roblox-assets-monitoring-system-rbxams)
    - [Table of Contents](#table-of-contents)
    - [Tech Stack](#tech-stack)
    - [Team Members](#team-members)
    - [Live Demo](#live-demo)
    - [Installation Guide](#installation-guide)
      - [Prerequisites Tools](#prerequisites-tools)
      - [Step 1. Clone this repository inside of `c:\xampp\htdocs`](#step-1-clone-this-repository-inside-of-cxampphtdocs)
      - [Step 2. Install project dependencies of frontend and backend](#step-2-install-project-dependencies-of-frontend-and-backend)
      - [Step 3. Add a .env file and put these lines of code in it.](#step-3-add-a-env-file-and-put-these-lines-of-code-in-it)
      - [Step 4. Setup virtual host environment to access development mode](#step-4-setup-virtual-host-environment-to-access-development-mode)
      - [Step 5. Run the development server.](#step-5-run-the-development-server)

---

### Tech Stack

- Languages
  - Native PHP 8.4
  - HTML
  - JavaScript
- Frameworks
  - TailwindCSS v4 
- Database
  - MySQL

---

### Team Members

<table>
  <tbody>
    <tr>
      <td align="center" valign="top" width="14.28%">
        <a href="https://github.com/incubusgeronimo">
          <img src="https://avatars.githubusercontent.com/u/164271830?v=4" width="100px;" alt="Renz Geronimo"/>
          <br/>
          <sub>
          <b>
            Renz Geronimo
          </b>
          </sub>
        </a>
      <td align="center" valign="top" width="14.28%">
        <a href="https://github.com/rapp0456/">
          <img src="https://avatars.githubusercontent.com/u/89532471?v=4" width="100px;" alt="Rafael Leonardo"/>
          <br/>
          <sub>
          <b>
            Rafael Leonardo
          </b>
          </sub>
        </a>
      </td>
      <td align="center" valign="top" width="14.28%">
        <a href="https://github.com/99lash">
          <img src="https://avatars.githubusercontent.com/u/124173983?v=4" width="100px;" alt="John Asher Manit"/>
          <br/>
          <sub>
          <b>
            John Asher Manit
          </b>
          </sub>
        </a>
      </td>
    </tr>
  </tbody>
</table>

---

### Live Demo

🔗 [https://rbxams.bscs3a.com](https://temporary-link.com)

---

### Installation Guide

A step by step guide that will tell you how to get the development environment up and running.

#### Prerequisites Tools

1. [XAMPP](https://www.apachefriends.org/download.html) for Apache & MySQL servers.
2. [Composer](https://getcomposer.org/) for PHP package manager.
3. [Node.js & NPM](https://nodejs.org/en/download) to manage TailwindCSS dependency.

#### Step 1. Clone this repository inside of `c:\xampp\htdocs`

```bash
  git clone https://github.com/99lash/RBXAMS.git
  cd RBXAMS
```


#### Step 2. Install project dependencies of frontend and backend

 **Backend dependencies**
```bash
  composer install
```

  **Frontend dependencies**
```bash
  npm install
```

#### Step 3. Add a .env file and put these lines of code in it.

```bash
# TESTING
MEOW="hello from dot env file"

# DATABASE CONNECTIOn
DB_HOST=127.0.0.1
DB_NAME=rbxams_db
DB_USERNAME=root
DB_PASSWORD=''
```

#### Step 4. Setup virtual host environment to access development mode
  4.1. Add .htaccess file inside the public_html folder and put these lines of code in it.
  
  ```bash
    <IfModule mod_rewrite.c>
      RewriteEngine On
      RewriteBase /

      # Protect system folders (just in case)
      RedirectMatch 403 ^/(src|config|vendor|storage|resources|node_modules|docs)/.*

      # If file/folder does not exist, route to index.php
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteRule ^ index.php [L]
      # RewriteRule ^(.*)$ index.php/$1 [L]
    </IfModule>   
  ```
  4.2. Open `httpd-vhost.conf` file located at `C:\xampp\apache\conf\extra\httpd-vhosts.conf` and put these lines of code at the bottom.

  ```bash
    <VirtualHost *:80>
        DocumentRoot "C:/xampp/htdocs"
        ServerName localhost

        <Directory "C:/xampp/htdocs">
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>

    <VirtualHost *:80>
        DocumentRoot "C:/xampp/htdocs/RBXAMS/public_html"
        ServerName rbxams.local

        <Directory "C:/xampp/htdocs/RBXAMS/public_html">
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>
  ```
  4.3. Open `hosts` file as an administrator located at `C:\Windows\System32\drivers\etc\hosts` and put this line of code in it.
  ```bash
    127.0.0.1 rbxams.local
  ```
  *NOTE: if you're running the Apache server from XAMPP, you must restart the XAMPP (apache) server.*

#### Step 5. Run the development server.
  
  5.1. Open XAMPP application and start Apache and MySQL server.
    
  You can now access `http://rbxams.local/`.

  5.2. If you are a *Frontend Developer*, execute this command in your terminal.
    
  ```bash
    npm run dev:bs
  ```
  You must access `http://localhost:3000/`.

  if the above script doesn't work as you followed all the steps accordingly. Then, alternatively run this instead.


  ```bash
    npm run dev
  ``` 
  You must access `http://rbxams.local/`.

---
