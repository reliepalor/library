# Imagick Extension Installation Guide for XAMPP on Windows

This guide helps you install the Imagick PHP extension for XAMPP on Windows (64-bit PHP 8.2).

---

## Step 1: Download and Install ImageMagick

1. Go to the official ImageMagick download page:  
   https://imagemagick.org/script/download.php#windows

2. Download the latest **ImageMagick-7 Q16 x64 DLL** installer (e.g., `ImageMagick-7.x.x-Q16-x64-dll.exe`).

3. Run the installer and follow the prompts.  
   - Make sure to check the option **"Add application directory to your system path"** during installation.

---

## Step 2: Download the PHP Imagick Extension DLL

1. Visit the PECL repository for Imagick:  
   https://windows.php.net/downloads/pecl/releases/imagick/

2. Find the version compatible with PHP 8.2, Thread Safe (TS), x64 architecture.  
   For example: `php_imagick-3.7.0-8.2-ts-vs16-x64.zip`

3. Download and extract the ZIP file.

---

## Step 3: Install the Imagick Extension

1. Copy the `php_imagick.dll` file from the extracted ZIP to your XAMPP PHP extensions directory:  
   `C:\xampp\php\ext\`

2. Open your `php.ini` file located at:  
   `C:\xampp\php\php.ini`

3. Add the following line to enable the extension:  
   ```ini
   extension=imagick
   ```

4. Save and close the `php.ini` file.

---

## Step 4: Restart Apache

- Use the XAMPP Control Panel to stop and start Apache to apply the changes.

---

## Step 5: Verify Installation

- Open a command prompt and run:  
  ```
  php -m | findstr imagick
  ```
- If `imagick` appears in the list, the extension is installed correctly.

---

## Troubleshooting

- Ensure the ImageMagick version matches your PHP architecture (x64).
- Make sure the Visual C++ Redistributable for Visual Studio 2019 or later is installed.
- Check Apache error logs for any startup errors related to Imagick.

---

If you need further assistance with the installation or configuration, feel free to ask.
