# SkillSwap User Manual - PDF Generation Guide

The user manual is ready! Here's how to get your PDF:

## Your Files Are Ready:

✅ **HTML Version**: `public/downloads/USER_MANUAL.html`
✅ **Markdown Version**: `USER_MANUAL.md`
✅ **Access via Web App**: Navigate to `/help` in your SkillSwap app

---

## Option 1: Print to PDF Using Your Browser (Easiest)

1. **Open the HTML file in your browser:**
   - Navigate to: `http://localhost/downloads/USER_MANUAL.html`
   - Or open: `public/downloads/USER_MANUAL.html` directly in your browser

2. **Print to PDF:**
   - Press `Ctrl + P` (or `Cmd + P` on Mac)
   - Select **"Save as PDF"** as your printer/destination
   - Click **"Save"**
   - Save as `USER_MANUAL.pdf` in `public/downloads/`

3. **Done!** Your PDF is created and users can download it from the app.

---

## Option 2: Use the Batch File (Windows)

1. Double-click: `convert-html-to-pdf.bat`
2. Microsoft Edge will open with the HTML manual
3. Press `Ctrl + P` to print
4. Select "Save as PDF" 
5. Save to: `public/downloads/USER_MANUAL.pdf`

---

## Option 3: Download a PDF Converter

If you want automated conversion, install one of these free tools:

- **wkhtmltopdf**: https://wkhtmltopdf.org/downloads.html
- **Pandoc**: https://pandoc.org/installing.html  
- **Print to PDF software**: Windows built-in or GNU Ghostscript

Then you can use command-line conversion commands.

---

## How Users Will Access It

Once you have the PDF in `public/downloads/`:

1. **Via Web App:**
   - Go to `/help` page
   - Click **"Download PDF"** button
   - Or click **"Read Online"** to view the HTML version

2. **Direct Download:**
   - URL: `http://localhost/help/download-pdf`

3. **View Online:**
   - URL: `http://localhost/help/manual`

---

## Files Created

```
skillswap/
├── public/downloads/
│   ├── USER_MANUAL.html        ← View online or as starting point
│   └── USER_MANUAL.pdf         ← Download this (create from HTML)
│
├── USER_MANUAL.md              ← Raw markdown content
├── USER_MANUAL.html            ← Original styled HTML
├── convert-html-to-pdf.bat     ← One-click converter (Windows)
│
└── app/
    ├── Controllers/HelpController.php    ← Handles /help routes
    └── Views/help/index.php               ← Help page with download links
```

---

## Questions?

- To view the manual in your app: Go to `http://localhost/help`
- To download the HTML: `http://localhost/downloads/USER_MANUAL.html`  
- Once PDF is created: Users can download from `/help/download-pdf`

The help system is fully integrated into your SkillSwap app! 🎉
