<?php

namespace oihana\graphics;

use function oihana\core\maths\gcd;

/**
 * Manages and enforces an aspect ratio for a given width and height.
 *
 * This class can represent dimensions and, when locked, will automatically adjust one dimension
 * when the other is changed to maintain the original aspect ratio.
 *
 * When unlocked, changing a dimension will result in a new aspect ratio being calculated.
 *
 * @package oihana\graphics
 * @author  Oihana
 * @version 1.0.0
 *
 * @example
 * ```php
 * // Create a new unlocked aspect ratio for a Full HD resolution (1920x1080)
 * $ratio = new AspectRatio(1920, 1080);
 *
 * echo "Initial Width: " . $ratio->getWidth() . "\n";      // 1920
 * echo "Initial Height: " . $ratio->getHeight() . "\n";     // 1080
 * echo "GCD: " . $ratio->getGCD() . "\n";                    // 120
 * // The simplified aspect ratio is 16:9 (1920/120 = 16, 1080/120 = 9)
 *
 * // Since it's unlocked, changing the width will change the ratio
 * $ratio->setWidth(1280);
 * echo "New Width: " . $ratio->getWidth() . "\n";        // 1280
 * echo "Height is unchanged: " . $ratio->getHeight() . "\n"; // 1080 (Ratio is now different)
 *
 * // ---
 *
 * // Now, create a locked aspect ratio
 * $lockedRatio = new AspectRatio(1920, 1080, true);
 * echo "Locked Ratio Width: " . $lockedRatio->getWidth() . "\n";  // 1920
 * echo "Locked Ratio Height: " . $lockedRatio->getHeight() . "\n"; // 1080
 *
 * // Change the width. The height will be automatically adjusted to maintain 16:9
 * $lockedRatio->setWidth(1280);
 * echo "New Locked Width: " . $lockedRatio->getWidth() . "\n";    // 1280
 * echo "New Locked Height: " . $lockedRatio->getHeight() . "\n"; // 720 (1280 * 9 / 16)
 *
 * // Lock an existing ratio and change the height
 * $ratio->lock();
 * $ratio->setHeight(900);
 * echo "Manually Locked Width: " . $ratio->getWidth() . "\n";  // 1600 (Recalculated from 1280x900 ratio)
 * echo "Manually Locked Height: " . $ratio->getHeight() . "\n"; // 900
 * ```
 */
class AspectRatio
{
    /**
     * AspectRatio constructor.
     *
     * Initializes the aspect ratio with a given width and height.
     *
     * Can be immediately locked to enforce the calculated ratio.
     *
     * @param float|int $width  The initial width.
     * @param float|int $height The initial height.
     * @param bool      $lock   If true, the aspect ratio is locked upon creation.
     */
    public function __construct( float|int $width = 0 , float|int $height = 0 , bool $lock = false )
    {
        $this->_width  = $width  ;
        $this->_height = $height ;
        $this->_locked = false   ;
        $this->recalculateGCD()  ;

        $this->_locked = $lock   ;

        if ($lock)
        {
            $this->_locked = true ;
            $this->_height = (int) round($this->_width * $this->_aspH / $this->_aspW ) ;
        }
    }

    /**
     * Gets the Greatest Common Divisor (GCD) of the current width and height.
     *
     * <p>This calculation casts the width and height to integers, ignoring any floating-point values.</p>
     */
    public int $gcd
    {
        get => $this->_gcd ;
    }

    /**
     * The current height size.
     *
     * If the object is locked, the width is automatically adjusted to maintain
     * the aspect ratio. If unlocked, the aspect ratio is recalculated based on
     * the new height and existing width.
     */
    public int $height
    {
        get => $this->_height ;
        set
        {
            $this->_height = $value ;
            if ( $this->_locked )
            {
                $this->_width = intval($this->_height * $this->_aspW / $this->_aspH ) ;
            }
            else
            {
                $this->recalculateGCD() ;
            }
        }
    }

    /**
     * The current width size.
     *
     * If the object is locked, the height is automatically adjusted to maintain
     * the aspect ratio. If unlocked, the aspect ratio is recalculated based on
     * the new width and existing height.
     */
    public int $width
    {
        get => $this->_width ;
        set
        {
            $this->_width = $value ;
            if ( $this->_locked )
            {
                $this->_height = intval($this->_width * $this->_aspH / $this->_aspW ) ;
            }
            else
            {
                $this->recalculateGCD() ;
            }
        }
    }

    /**
     * Checks if the aspect ratio is currently locked.
     *
     * @return bool True if the aspect ratio is locked, false otherwise.
     */
    public function isLocked():bool
    {
        return $this->_locked ;
    }

    /**
     * Locks the aspect ratio.
     *
     * After calling this method, any subsequent changes to width or height
     * will proportionally adjust the other dimension to maintain the current ratio.
     *
     * @return void
     */
    public function lock():void
    {
        $this->_locked = true ;
    }

    /**
     * Unlocks the aspect ratio.
     *
     * After calling this method, changing the width or height will no longer
     * affect the other dimension, and the ratio will be recalculated instead.
     *
     * @return void
     */
    public function unlock():void
    {
        $this->_locked = false ;
    }

    private int   $_aspW ;
    private int   $_aspH ;
    private int   $_gcd ;
    protected int $_height ;
    private bool  $_locked ;
    protected int $_width ;

    /**
     * Recalculates the GCD and the simplified aspect ratio components.
     *
     * This private method is called during initialization and whenever a
     * dimension is changed while the object is in an unlocked state.
     *
     * @return void
     */
    private function recalculateGCD(): void
    {
        $this->_gcd  = gcd( $this->_width , $this->_height ) ?: 1 ;
        $this->_aspW = intdiv( $this->_width, $this->_gcd  ) ;
        $this->_aspH = intdiv( $this->_height, $this->_gcd );
    }
}