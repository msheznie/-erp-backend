<?php

namespace App\helper;

class FileSecurityValidator
{
    /**
     * Validate file content for security threats including XSS, malware, and other attacks
     *
     * @param string $fileContent The file content
     * @param string $fileExtension The file extension
     * @param string $mimeType The MIME type (optional)
     * @return array ['isValid' => bool, 'message' => string, 'threats' => array]
     */
    public static function validateFileContent($fileContent, $fileExtension, $mimeType = null)
    {
        $threats = [];
        $extension = strtolower($fileExtension);
        
        try {
            // // 1. Check for malicious file signatures
            // $signatureCheck = self::checkMaliciousSignatures($fileContent, $extension);
            // if (!$signatureCheck['isValid']) {
            //     $threats = array_merge($threats, $signatureCheck['threats']);
            // }

            // 2. Check for XSS and script injection
            $xssCheck = self::checkXssAndScripts($fileContent, $extension);
            if (!$xssCheck['isValid']) {
                $threats = array_merge($threats, $xssCheck['threats']);
            }

            // // 3. Check for embedded executables
            // $executableCheck = self::checkEmbeddedExecutables($fileContent, $extension);
            // if (!$executableCheck['isValid']) {
            //     $threats = array_merge($threats, $executableCheck['threats']);
            // }

            // // 4. Check for malicious URLs and redirects
            // $urlCheck = self::checkMaliciousUrls($fileContent, $extension);
            // if (!$urlCheck['isValid']) {
            //     $threats = array_merge($threats, $urlCheck['threats']);
            // }

            // // 5. Check for data exfiltration patterns
            // $exfiltrationCheck = self::checkDataExfiltration($fileContent, $extension);
            // if (!$exfiltrationCheck['isValid']) {
            //     $threats = array_merge($threats, $exfiltrationCheck['threats']);
            // }

            // // 6. File-specific validation
            // $fileSpecificCheck = self::validateFileSpecific($fileContent, $extension, $mimeType);
            // if (!$fileSpecificCheck['isValid']) {
            //     $threats = array_merge($threats, $fileSpecificCheck['threats']);
            // }

            if (empty($threats)) {
                return [
                    'isValid' => true,
                    'message' => 'File content is safe',
                    'threats' => []
                ];
            } else {
                return [
                    'isValid' => false,
                    'message' => 'File contains potential security threats: ' . implode(', ', array_unique($threats)),
                    'threats' => array_unique($threats)
                ];
            }

        } catch (\Exception $e) {
            return [
                'isValid' => false,
                'message' => 'Error validating file content: ' . $e->getMessage(),
                'threats' => ['validation_error']
            ];
        }
    }

    /**
     * Check for malicious file signatures and magic bytes
     */
    private static function checkMaliciousSignatures($content, $extension)
    {
        $threats = [];
        
        // Check for executable signatures
        $executableSignatures = [
            'MZ' => 'PE executable (Windows)',
            "\x7fELF" => 'ELF executable (Linux)',
            "\xfe\xed\xfa" => 'Mach-O executable (macOS)',
            "\xca\xfe\xba\xbe" => 'Java class file',
            "\x50\x4b\x03\x04" => 'ZIP/JAR file (potential executable)',
            "\x4d\x5a" => 'DOS executable',
        ];

        foreach ($executableSignatures as $signature => $description) {
            if (strpos($content, $signature) === 0) {
                $threats[] = "Executable signature detected: $description";
            }
        }

        // Check for script signatures in non-script files
        if (!in_array($extension, ['js', 'php', 'asp', 'aspx', 'jsp', 'py', 'rb', 'pl', 'sh', 'bat', 'cmd', 'ps1'])) {
            $scriptSignatures = [
                '<?php' => 'PHP script',
                '<script' => 'JavaScript',
                '<%' => 'ASP script',
                '#!/' => 'Shell script shebang',
                '#!/usr/bin/env' => 'Script with shebang',
            ];

            foreach ($scriptSignatures as $signature => $description) {
                if (strpos($content, $signature) !== false) {
                    $threats[] = "Script signature in non-script file: $description";
                }
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Check for XSS and script injection patterns
     */
    private static function checkXssAndScripts($content, $extension)
    {
        $threats = [];
        
        // Common XSS patterns
        $xssPatterns = [
            // JavaScript patterns
            '/<script[^>]*>.*?<\/script>/is' => 'JavaScript script tag',
            '/javascript:/i' => 'JavaScript URL',
            '/vbscript:/i' => 'VBScript URL',
            '/data:text\/html/i' => 'Data URI with HTML',
            '/data:application\/javascript/i' => 'Data URI with JavaScript',
            
            // Event handlers
            '/on\w+\s*=\s*["\'][^"\']*["\']/i' => 'Event handler attribute',
            '/onload\s*=/i' => 'onload event handler',
            '/onerror\s*=/i' => 'onerror event handler',
            '/onclick\s*=/i' => 'onclick event handler',
            '/onmouseover\s*=/i' => 'onmouseover event handler',
            '/onfocus\s*=/i' => 'onfocus event handler',
            '/onblur\s*=/i' => 'onblur event handler',
            
            // HTML injection
            '/<iframe[^>]*>.*?<\/iframe>/is' => 'iframe tag',
            '/<object[^>]*>.*?<\/object>/is' => 'object tag',
            '/<embed[^>]*>.*?<\/embed>/is' => 'embed tag',
            '/<applet[^>]*>.*?<\/applet>/is' => 'applet tag',
            '/<form[^>]*>.*?<\/form>/is' => 'form tag',
            '/<input[^>]*>.*?<\/input>/is' => 'input tag',
            '/<button[^>]*>.*?<\/button>/is' => 'button tag',
            '/<textarea[^>]*>.*?<\/textarea>/is' => 'textarea tag',
            '/<select[^>]*>.*?<\/select>/is' => 'select tag',
            
            // CSS injection
            '/<style[^>]*>.*?<\/style>/is' => 'style tag',
            '/<link[^>]*rel\s*=\s*["\']stylesheet["\'][^>]*>/i' => 'stylesheet link',
            '/expression\s*\(/i' => 'CSS expression',
            '/url\s*\(\s*["\']?javascript:/i' => 'CSS with JavaScript URL',
            
            // Meta refresh redirects
            '/<meta[^>]*http-equiv\s*=\s*["\']refresh["\'][^>]*>/i' => 'Meta refresh redirect',
            
            // Dangerous functions
            '/eval\s*\(/i' => 'eval function',
            '/document\.write/i' => 'document.write function',
            '/window\.open/i' => 'window.open function',
            '/location\.href/i' => 'location.href property',
            '/document\.cookie/i' => 'document.cookie access',
            '/XMLHttpRequest/i' => 'XMLHttpRequest object',
            '/fetch\s*\(/i' => 'fetch function',
            '/setTimeout\s*\(/i' => 'setTimeout function',
            '/setInterval\s*\(/i' => 'setInterval function',
        ];

        foreach ($xssPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $threats[] = "XSS pattern detected: $description";
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Check for embedded executables and binary content
     */
    private static function checkEmbeddedExecutables($content, $extension)
    {
        $threats = [];
        
        // Check for embedded executables in document files
        if (in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'rtf', 'odt', 'ods', 'odp'])) {
            // Look for executable signatures within the content
            $executablePatterns = [
                '/MZ.{0,100}PE/' => 'Embedded PE executable',
                '/\x7fELF/' => 'Embedded ELF executable',
                '/\xfe\xed\xfa/' => 'Embedded Mach-O executable',
                '/\xca\xfe\xba\xbe/' => 'Embedded Java class',
                '/PK.{0,20}META-INF\/MANIFEST\.MF/' => 'Embedded JAR file',
            ];

            foreach ($executablePatterns as $pattern => $description) {
                if (preg_match($pattern, $content)) {
                    $threats[] = "Embedded executable detected: $description";
                }
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Check for malicious URLs and redirects
     */
    private static function checkMaliciousUrls($content, $extension)
    {
        $threats = [];
        
        $maliciousUrlPatterns = [
            // JavaScript URLs
            '/javascript:/i' => 'JavaScript URL',
            '/vbscript:/i' => 'VBScript URL',
            '/data:text\/html/i' => 'Data URI with HTML',
            '/data:application\/javascript/i' => 'Data URI with JavaScript',
            
            // Suspicious protocols
            '/file:\/\//i' => 'File protocol URL',
            '/ftp:\/\//i' => 'FTP protocol URL',
            '/gopher:\/\//i' => 'Gopher protocol URL',
            
            // Common XSS payloads in URLs
            '/<script/i' => 'Script tag in URL',
            '/onload=/i' => 'Event handler in URL',
            '/onerror=/i' => 'Event handler in URL',
            '/onclick=/i' => 'Event handler in URL',
            
            // Suspicious domains and IPs
            '/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b/' => 'IP address in content',
            '/localhost/i' => 'Localhost reference',
            '/127\.0\.0\.1/i' => 'Localhost IP',
            '/0\.0\.0\.0/i' => 'Zero IP address',
        ];

        foreach ($maliciousUrlPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $threats[] = "Malicious URL pattern: $description";
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Check for data exfiltration patterns
     */
    private static function checkDataExfiltration($content, $extension)
    {
        $threats = [];
        
        $exfiltrationPatterns = [
            // Network requests
            '/XMLHttpRequest/i' => 'XMLHttpRequest for data exfiltration',
            '/fetch\s*\(/i' => 'Fetch API for data exfiltration',
            '/navigator\.sendBeacon/i' => 'Beacon API for data exfiltration',
            
            // Form submissions
            '/<form[^>]*action\s*=\s*["\'][^"\']*["\'][^>]*>/i' => 'Form with external action',
            '/method\s*=\s*["\']post["\']/i' => 'POST method form',
            
            // Image loading for data exfiltration
            '/<img[^>]*src\s*=\s*["\'][^"\']*["\'][^>]*>/i' => 'Image tag for data exfiltration',
            '/background\s*:\s*url\s*\(/i' => 'CSS background URL',
            
            // WebSocket connections
            '/WebSocket/i' => 'WebSocket connection',
            '/ws:\/\//i' => 'WebSocket URL',
            '/wss:\/\//i' => 'Secure WebSocket URL',
        ];

        foreach ($exfiltrationPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $threats[] = "Data exfiltration pattern: $description";
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * File-specific validation based on file type
     */
    private static function validateFileSpecific($content, $extension, $mimeType = null)
    {
        $threats = [];
        
        switch ($extension) {
            case 'pdf':
                $pdfValidation = self::validatePdfContent($content);
                if (!$pdfValidation['isValid']) {
                    $threats[] = $pdfValidation['message'];
                }
                break;
                
            case 'html':
            case 'htm':
                $htmlValidation = self::validateHtmlContent($content);
                if (!$htmlValidation['isValid']) {
                    $threats = array_merge($threats, $htmlValidation['threats']);
                }
                break;
                
            case 'xml':
                $xmlValidation = self::validateXmlContent($content);
                if (!$xmlValidation['isValid']) {
                    $threats = array_merge($threats, $xmlValidation['threats']);
                }
                break;
                
            case 'svg':
                $svgValidation = self::validateSvgContent($content);
                if (!$svgValidation['isValid']) {
                    $threats = array_merge($threats, $svgValidation['threats']);
                }
                break;
                
            case 'js':
                $jsValidation = self::validateJavaScriptContent($content);
                if (!$jsValidation['isValid']) {
                    $threats = array_merge($threats, $jsValidation['threats']);
                }
                break;
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Validate PDF content (reuse existing logic)
     */
    private static function validatePdfContent($content)
    {
        // Check if content starts with PDF header
        if (strpos($content, '%PDF-') !== 0) {
            return [
                'isValid' => false,
                'message' => 'Invalid PDF file format'
            ];
        }

        // Check for JavaScript in PDF
        $jsPatterns = [
            '/\/S\s*\/JavaScript/i',
            '/\/JS\s*\(/i',
            '/\/JavaScript\s*\(/i',
            '/\/OpenAction/i',
            '/\/AA\s*<<.*?\/S\s*\/JavaScript/i',
        ];

        foreach ($jsPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return [
                    'isValid' => false,
                    'message' => 'PDF contains JavaScript code'
                ];
            }
        }

        return ['isValid' => true, 'message' => 'PDF content is safe'];
    }

    /**
     * Validate HTML content
     */
    private static function validateHtmlContent($content)
    {
        $threats = [];
        
        // Check for dangerous HTML elements
        $dangerousElements = [
            '/<script[^>]*>.*?<\/script>/is' => 'Script tag',
            '/<iframe[^>]*>.*?<\/iframe>/is' => 'iframe tag',
            '/<object[^>]*>.*?<\/object>/is' => 'object tag',
            '/<embed[^>]*>.*?<\/embed>/is' => 'embed tag',
            '/<applet[^>]*>.*?<\/applet>/is' => 'applet tag',
            '/<form[^>]*>.*?<\/form>/is' => 'form tag',
            '/<input[^>]*>.*?<\/input>/is' => 'input tag',
            '/<button[^>]*>.*?<\/button>/is' => 'button tag',
        ];

        foreach ($dangerousElements as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $threats[] = "Dangerous HTML element: $description";
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Validate XML content
     */
    private static function validateXmlContent($content)
    {
        $threats = [];
        
        // Check for XXE (XML External Entity) attacks
        $xxePatterns = [
            '/<!DOCTYPE[^>]*\[[^>]*<!ENTITY[^>]*>/i' => 'XML External Entity declaration',
            '/&[a-zA-Z0-9_-]+;/' => 'Entity reference',
            '/<!ENTITY[^>]*>/i' => 'Entity declaration',
        ];

        foreach ($xxePatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $threats[] = "XXE vulnerability: $description";
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Validate SVG content
     */
    private static function validateSvgContent($content)
    {
        $threats = [];
        
        // Check for dangerous SVG elements and attributes
        $dangerousSvgPatterns = [
            '/<script[^>]*>.*?<\/script>/is' => 'Script tag in SVG',
            '/<iframe[^>]*>.*?<\/iframe>/is' => 'iframe tag in SVG',
            '/<object[^>]*>.*?<\/object>/is' => 'object tag in SVG',
            '/<embed[^>]*>.*?<\/embed>/is' => 'embed tag in SVG',
            '/onload\s*=/i' => 'onload event in SVG',
            '/onerror\s*=/i' => 'onerror event in SVG',
            '/onclick\s*=/i' => 'onclick event in SVG',
            '/javascript:/i' => 'JavaScript URL in SVG',
            '/vbscript:/i' => 'VBScript URL in SVG',
        ];

        foreach ($dangerousSvgPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $threats[] = "Dangerous SVG content: $description";
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Validate JavaScript content
     */
    private static function validateJavaScriptContent($content)
    {
        $threats = [];
        
        // Check for dangerous JavaScript patterns
        $dangerousJsPatterns = [
            '/eval\s*\(/i' => 'eval function',
            '/Function\s*\(/i' => 'Function constructor',
            '/setTimeout\s*\(/i' => 'setTimeout function',
            '/setInterval\s*\(/i' => 'setInterval function',
            '/document\.write/i' => 'document.write function',
            '/innerHTML\s*=/i' => 'innerHTML assignment',
            '/outerHTML\s*=/i' => 'outerHTML assignment',
            '/document\.createElement/i' => 'createElement function',
            '/XMLHttpRequest/i' => 'XMLHttpRequest object',
            '/fetch\s*\(/i' => 'fetch function',
            '/navigator\.sendBeacon/i' => 'sendBeacon function',
            '/window\.open/i' => 'window.open function',
            '/location\.href/i' => 'location.href property',
            '/document\.cookie/i' => 'document.cookie access',
        ];

        foreach ($dangerousJsPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $threats[] = "Dangerous JavaScript pattern: $description";
            }
        }

        return [
            'isValid' => empty($threats),
            'threats' => $threats
        ];
    }

    /**
     * Get allowed file extensions for upload
     */
    public static function getAllowedExtensions()
    {
        return [
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'tif', 'webp', 'svg',
            
            // Documents
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf',
            'odt', 'ods', 'odp', 'csv',
            
            // Web files (with strict content validation)
            'html', 'htm', 'js',
            
            // Archives
            'zip', 'rar', '7z', 'tar', 'gz',
            
            // Audio/Video
            'mp3', 'mp4', 'avi', 'mov', 'wmv', 'flv', 'wav', 'ogg',
            
            // Other safe formats
            'json', 'xml', 'yaml', 'yml'
        ];
    }

    /**
     * Check if file extension is allowed
     */
    public static function isExtensionAllowed($extension)
    {
        $allowedExtensions = self::getAllowedExtensions();
        return in_array(strtolower($extension), $allowedExtensions);
    }
}
