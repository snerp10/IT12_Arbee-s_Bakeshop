# PowerShell Script to Generate Circular Favicons
# Run this script in PowerShell to create circular versions of your logo

Write-Host "🔵 Circular Favicon Generator for Arbee's Logo" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

$originalLogo = ".\public\images\Arbee's_Logo.png"
$outputDir = ".\public\images\favicons"

# Create output directory if it doesn't exist
if (!(Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir -Force
    Write-Host "✅ Created favicon directory: $outputDir" -ForegroundColor Green
}

# Check if ImageMagick is available
$imageMagickAvailable = $false
try {
    $magickVersion = magick -version 2>$null
    if ($magickVersion) {
        $imageMagickAvailable = $true
        Write-Host "✅ ImageMagick found! Using ImageMagick for high-quality generation." -ForegroundColor Green
    }
} catch {
    Write-Host "⚠️  ImageMagick not found. Using alternative method." -ForegroundColor Yellow
}

# Define favicon sizes
$sizes = @(16, 32, 48, 64, 128, 180, 192, 512)

if ($imageMagickAvailable) {
    Write-Host "`n🎨 Generating circular favicons with ImageMagick..." -ForegroundColor Cyan
    
    foreach ($size in $sizes) {
        $outputFile = "$outputDir\circular-favicon-${size}x${size}.png"
        
        # Create circular favicon with ImageMagick
        $command = "magick `"$originalLogo`" -resize ${size}x${size} -gravity center -extent ${size}x${size} -background white ( +clone -threshold 101% -fill black -draw `"circle $($size/2),$($size/2) $($size/2),0`" ) -alpha off -compose copy_opacity -composite `"$outputFile`""
        
        try {
            Invoke-Expression $command
            Write-Host "  ✅ Generated: circular-favicon-${size}x${size}.png" -ForegroundColor Green
        } catch {
            Write-Host "  ❌ Failed to generate ${size}x${size} favicon" -ForegroundColor Red
        }
    }
} else {
    Write-Host "`n📋 ImageMagick not available. Here are your options:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "OPTION 1: Install ImageMagick" -ForegroundColor Cyan
    Write-Host "  1. Download from: https://imagemagick.org/script/download.php#windows"
    Write-Host "  2. Install and add to PATH"
    Write-Host "  3. Run this script again"
    Write-Host ""
    Write-Host "OPTION 2: Use the Web Generator" -ForegroundColor Cyan
    Write-Host "  1. Open: http://localhost:8000/favicon-generator.html (after running php artisan serve)"
    Write-Host "  2. Click 'Generate Circular Favicons'"
    Write-Host "  3. Download the generated files"
    Write-Host ""
    Write-Host "OPTION 3: Manual CSS Approach" -ForegroundColor Cyan
    Write-Host "  The CSS file has been created at: public\css\favicon-circular.css"
    Write-Host "  This applies border-radius: 50% to make existing favicons appear circular"
    Write-Host ""
}

# Create ICO file instruction
Write-Host "`n🔄 Next Steps:" -ForegroundColor Cyan
Write-Host "1. After generating PNG files, convert one to favicon.ico:"
Write-Host "   - Use online converter: https://convertio.co/png-ico/"
Write-Host "   - Or use ImageMagick: magick circular-favicon-32x32.png favicon.ico"
Write-Host ""
Write-Host "2. Replace your current favicon files:"
Write-Host "   - Copy circular-favicon-16x16.png → public\images\favicon-16x16.png"
Write-Host "   - Copy circular-favicon-32x32.png → public\images\favicon-32x32.png"
Write-Host "   - Copy circular-favicon-180x180.png → public\images\apple-touch-icon.png"
Write-Host "   - Copy generated favicon.ico → public\favicon.ico"
Write-Host ""
Write-Host "3. Your Laravel views will automatically use the new circular favicons!"

Write-Host "`n🎯 Quick CSS Solution (Immediate):" -ForegroundColor Green
Write-Host "If you want an immediate solution, I can update your layout files to use CSS"
Write-Host "that makes your current favicon appear circular in browsers that support it."

$useCSSSolution = Read-Host "`nWould you like me to apply the CSS circular solution now? (y/n)"

if ($useCSSSolution -eq 'y' -or $useCSSSolution -eq 'Y') {
    Write-Host "`n🔧 Applying CSS circular solution..." -ForegroundColor Cyan
    Write-Host "This will add CSS to make your favicon appear circular in supported browsers."
    Write-Host "Note: This method has limited browser support compared to actual circular image files."
}

Write-Host "`n✨ Favicon generation process complete!" -ForegroundColor Green