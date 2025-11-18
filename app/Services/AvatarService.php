<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    /**
     * Generate a placeholder avatar URL
     */
    public static function getPlaceholderAvatar(?string $name = null, int $size = 300): string
    {
        // Use UI Avatars service for generating avatars
        $initials = $name ? self::getInitials($name) : 'U';
        $background = self::generateColorFromString($name ?? 'default');
        
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . 
               "&background=" . $background . 
               "&color=fff&size=" . $size . 
               "&rounded=true&bold=true";
    }

    /**
     * Get initials from name
     */
    private static function getInitials(string $name): string
    {
        $parts = explode(' ', trim($name));
        $initials = '';
        
        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        
        return $initials ?: 'U';
    }

    /**
     * Generate consistent color from string
     */
    private static function generateColorFromString(string $string): string
    {
        $colors = [
            '1abc9c', '2ecc71', '3498db', '9b59b6', 'e74c3c',
            'f39c12', '34495e', '16a085', '27ae60', '2980b9',
            '8e44ad', 'c0392b', 'd35400', '7f8c8d', '95a5a6'
        ];
        
        $hash = crc32($string);
        $index = abs($hash) % count($colors);
        
        return $colors[$index];
    }

    /**
     * Generate a placeholder avatar URL with custom background color
     */
    public static function getPlaceholderAvatarWithColor(?string $name = null, int $size = 300, string $backgroundColor = 'random'): string
    {
        // Use UI Avatars service for generating avatars
        $initials = $name ? self::getInitials($name) : 'U';

        // If backgroundColor is 'random', use the existing color generation logic
        if ($backgroundColor === 'random') {
            $backgroundColor = self::generateColorFromString($name ?? 'default');
        }

        return "https://ui-avatars.com/api/?name=" . urlencode($initials) .
               "&background=" . urlencode($backgroundColor) .
               "&color=fff&size=" . $size .
               "&rounded=true&bold=true";
    }

    /**
     * Get profile picture URL with fallback
     */
    public static function getProfilePictureUrl(?string $profilePicture, ?string $name = null, int $size = 300): string
    {
        if ($profilePicture) {
            // Check if file exists in public/storage (where uploaded files are stored)
            $publicPath = public_path('storage/' . $profilePicture);
            if (file_exists($publicPath)) {
                return asset('storage/' . $profilePicture);
            }
            
            // Also check storage/app/public (Laravel's default storage location)
            if (Storage::disk('public')->exists($profilePicture)) {
                return asset('storage/' . $profilePicture);
            }
        }

        // If profile picture is not found, try to generate placeholder avatar
        return self::getPlaceholderAvatar($name, $size);
    }
}
