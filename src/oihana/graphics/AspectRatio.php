<?php

declare(strict_types=1);

namespace oihana\graphics;

use InvalidArgumentException;

use function oihana\core\maths\gcd;

/**
 * Represents and manages a 2D aspect ratio.
 *
 * This class stores a width and height pair and optionally enforces
 * a locked aspect ratio behavior.
 *
 * When the ratio is unlocked:
 *
 * - Changing the width does not affect the height.
 * - Changing the height does not affect the width.
 * - The internal aspect ratio is recalculated automatically.
 *
 * When the ratio is locked:
 *
 * - Changing the width automatically recalculates the height.
 * - Changing the height automatically recalculates the width.
 * - The simplified aspect ratio captured at lock time is preserved
 *   and is not re-derived from rounded dimensions.
 *
 * The aspect ratio is internally simplified using the Greatest Common Divisor (GCD).
 *
 * Example:
 *
 * ```php
 * use oihana\graphics\AspectRatio;
 *
 * // Create a new unlocked aspect ratio
 * $ratio = new AspectRatio(1920, 1080);
 *
 * echo $ratio->width  . PHP_EOL; // 1920
 * echo $ratio->height . PHP_EOL; // 1080
 * echo $ratio->ratio();          // 16:9
 *
 * // Since the ratio is unlocked,
 * // changing the width does not affect the height.
 *
 * $ratio->width = 1280;
 *
 * echo $ratio->width  . PHP_EOL; // 1280
 * echo $ratio->height . PHP_EOL; // 1080
 * echo $ratio->ratio();          // 32:27
 *
 * // ----------------------------------------------------------------
 *
 * // Create a locked aspect ratio.
 *
 * $locked = new AspectRatio(1920, 1080, true);
 *
 * echo $locked->ratio(); // 16:9
 *
 * // Changing the width automatically updates the height.
 *
 * $locked->width = 1280;
 *
 * echo $locked->width  . PHP_EOL; // 1280
 * echo $locked->height . PHP_EOL; // 720
 *
 * // Changing the height automatically updates the width.
 *
 * $locked->height = 900;
 *
 * echo $locked->width  . PHP_EOL; // 1600
 * echo $locked->height . PHP_EOL; // 900
 *
 * // ----------------------------------------------------------------
 *
 * // Lock an existing ratio.
 *
 * $ratio->lock();
 *
 * $ratio->height = 900;
 *
 * echo $ratio->width  . PHP_EOL; // 1067
 * echo $ratio->height . PHP_EOL; // 900
 * ```
 *
 * @package oihana\graphics
 *
 * @author    Oihana
 * @copyright Oihana
 * @license   MIT
 *
 * @version 1.1.0
 */
class AspectRatio
{
    /**
     * Creates a new AspectRatio instance.
     *
     * @param int  $width  The initial width.
     * @param int  $height The initial height.
     * @param bool $lock   Indicates whether the ratio must be locked.
     *
     * @throws InvalidArgumentException If width or height is negative.
     */
    public function __construct
    (
        int  $width  = 0 ,
        int  $height = 0 ,
        bool $lock   = false
    )
    {
        $this->assertDimension( $width  , self::WIDTH  ) ;
        $this->assertDimension( $height , self::HEIGHT ) ;

        $this->_width  = $width;
        $this->_height = $height;
        $this->_locked = $lock;

        $this->recalculateRatio();

        if ($this->_locked)
        {
            $this->synchronizeHeight();
        }
    }

    /**
     * The 'aspectHeight' component expression.
     */
    public const string ASPECT_HEIGHT = 'aspectHeight' ;

    /**
     * The 'aspectWidth' component expression.
     */
    public const string ASPECT_WIDTH = 'aspectWidth' ;

    /**
     * The 'height' component expression.
     */
    public const string HEIGHT = 'height' ;

    /**
     * The 'locked' component expression.
     */
    public const string LOCKED = 'locked' ;

    /**
     * The 'ratio' component expression.
     */
    public const string RATIO = 'ratio' ;

    /**
     * The 'width' component expression.
     */
    public const string WIDTH = 'width' ;

    /**
     * Returns the simplified aspect ratio height component.
     *
     * Example:
     *
     * ```php
     * $ratio = new AspectRatio(1920, 1080);
     *
     * echo $ratio->aspectHeight; // 9
     * ```
     */
    public int $aspectHeight
    {
        get => $this->_aspH;
    }

    /**
     * Returns the simplified aspect ratio width component.
     *
     * Example:
     *
     * ```php
     * $ratio = new AspectRatio(1920, 1080);
     *
     * echo $ratio->aspectWidth; // 16
     * ```
     */
    public int $aspectWidth
    {
        get => $this->_aspW;
    }

    /**
     * Gets the current Greatest Common Divisor (GCD).
     *
     * The GCD is used internally to simplify the ratio.
     *
     * When the ratio is locked, the GCD reflects the value computed
     * at lock time and is not refreshed on subsequent dimension changes.
     *
     * Example:
     *
     * ```php
     * $ratio = new AspectRatio(1920, 1080);
     *
     * echo $ratio->gcd; // 120
     * ```
     */
    public int $gcd
    {
        get => $this->_gcd;
    }

    /**
     * Gets or sets the current height.
     *
     * When the aspect ratio is locked, updating the height automatically
     * recalculates the width to preserve the current ratio.
     *
     * When unlocked, the aspect ratio is recalculated instead.
     *
     * @throws InvalidArgumentException If the height is negative.
     */
    public int $height
    {
        get => $this->_height;
        set { $this->setHeight( $value ) ; }
    }

    /**
     * Indicates whether the aspect ratio is currently locked.
     *
     * Example:
     *
     * ```php
     * $ratio = new AspectRatio(1920, 1080, true);
     *
     * echo $ratio->locked; // true
     * ```
     */
    public bool $locked
    {
        get => $this->_locked ;
    }

    /**
     * Gets or sets the current width.
     *
     * When the aspect ratio is locked, updating the width automatically
     * recalculates the height to preserve the current ratio.
     *
     * When unlocked, the aspect ratio is recalculated instead.
     *
     * @throws InvalidArgumentException If the width is negative.
     */
    public int $width
    {
        get => $this->_width;
        set { $this->setWidth( $value ) ; }
    }

    /**
     * Returns a string representation of the dimensions and ratio.
     *
     * Example:
     *
     * ```php
     * $ratio = new AspectRatio(1920, 1080);
     *
     * echo $ratio;
     *
     * // 1920x1080 (16:9)
     * ```
     */
    public function __toString(): string
    {
        return sprintf
        (
            '%dx%d (%s)',
            $this->_width,
            $this->_height,
            $this->ratio()
        );
    }

    /**
     * Creates an AspectRatio instance from a simplified ratio.
     *
     * Example:
     *
     * ```php
     * $ratio = AspectRatio::fromRatio(16, 9, 1920);
     *
     * echo $ratio; // 1920x1080 (16:9)
     * ```
     *
     * @param int  $aspectWidth  The ratio width component. Must be greater than 0.
     * @param int  $aspectHeight The ratio height component. Must be greater than 0.
     * @param int  $width        The desired width. Must be greater than 0.
     * @param bool $lock         Indicates whether the ratio should be locked.
     *
     * @throws InvalidArgumentException If any of $aspectWidth, $aspectHeight or $width is not positive.
     */
    public static function fromRatio
    (
        int  $aspectWidth ,
        int  $aspectHeight ,
        int  $width ,
        bool $lock = true
    ): self
    {
        self::assertPositive( $aspectWidth  , 'aspect width'  ) ;
        self::assertPositive( $aspectHeight , 'aspect height' ) ;
        self::assertPositive( $width        , self::WIDTH     ) ;

        $height = (int) round
        (
            $width * $aspectHeight / $aspectWidth
        );

        return new self($width, $height, $lock);
    }

    /**
     * Locks the current aspect ratio.
     *
     * Once locked:
     *
     * - Updating the width automatically updates the height.
     * - Updating the height automatically updates the width.
     *
     * The current simplified ratio becomes the preserved ratio.
     *
     * @return $this
     */
    public function lock(): static
    {
        $this->_locked = true;

        return $this;
    }

    /**
     * Returns the simplified ratio as a string.
     *
     * Example:
     *
     * ```php
     * $ratio = new AspectRatio(1920, 1080);
     *
     * echo $ratio->ratio(); // 16:9
     * ```
     */
    public function ratio(): string
    {
        return $this->_aspW . ':' . $this->_aspH;
    }

    /**
     * Updates the height.
     *
     * When the ratio is locked, the width is synchronized from the
     * snapshot aspect ratio and the snapshot is preserved.
     *
     * When unlocked, the aspect ratio is recalculated from the new
     * width/height pair.
     *
     * @param int $height
     *
     * @throws InvalidArgumentException If the height is negative.
     */
    public function setHeight( int $height ): void
    {
        $this->assertDimension( $height , self::HEIGHT ) ;

        $this->_height = $height;

        if ( $this->_locked )
        {
            $this->synchronizeWidth() ;
        }
        else
        {
            $this->recalculateRatio() ;
        }
    }

    /**
     * Updates the width.
     *
     * When the ratio is locked, the height is synchronized from the
     * snapshot aspect ratio and the snapshot is preserved.
     *
     * When unlocked, the aspect ratio is recalculated from the new
     * width/height pair.
     *
     * @param int $width
     *
     * @throws InvalidArgumentException If the width is negative.
     */
    public function setWidth( int $width ): void
    {
        $this->assertDimension( $width , self::WIDTH  ) ;

        $this->_width = $width ;

        if ( $this->_locked )
        {
            $this->synchronizeHeight() ;
        }
        else
        {
            $this->recalculateRatio() ;
        }
    }

    /**
     * Returns the current dimensions as an associative array.
     *
     * Example:
     *
     * ```php
     * $ratio = new AspectRatio(1920, 1080);
     *
     * print_r($ratio->toArray());
     *
     * // [
     * //     'width'       => 1920,
     * //     'height'      => 1080,
     * //     'aspectWidth' => 16,
     * //     'aspectHeight'=> 9,
     * //     'ratio'       => '16:9',
     * //     'locked'      => false
     * // ]
     * ```
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return
        [
            self::WIDTH         => $this->_width  ,
            self::HEIGHT        => $this->_height ,
            self::ASPECT_WIDTH  => $this->_aspW   ,
            self::ASPECT_HEIGHT => $this->_aspH   ,
            self::RATIO         => $this->ratio() ,
            self::LOCKED        => $this->_locked ,
        ];
    }

    /**
     * Unlocks the aspect ratio.
     *
     * Once unlocked:
     *
     * - Width and height become independent.
     * - The aspect ratio is recalculated after each modification.
     *
     * @return $this
     */
    public function unlock(): static
    {
        $this->_locked = false;

        return $this;
    }

    /**
     * Simplified aspect ratio height component.
     */
    private int $_aspH = 0;

    /**
     * Simplified aspect ratio width component.
     */
    private int $_aspW = 0;

    /**
     * Greatest Common Divisor.
     */
    private int $_gcd = 1;

    /**
     * Current height.
     */
    protected int $_height = 0;

    /**
     * Indicates whether the ratio is locked.
     */
    private bool $_locked = false;

    /**
     * Current width.
     */
    protected int $_width = 0;

    /**
     * Validates a dimension value (non-negative).
     *
     * @param int    $value
     * @param string $name
     *
     * @throws InvalidArgumentException
     */
    private function assertDimension( int $value , string $name ): void
    {
        if ( $value < 0 )
        {
            throw new InvalidArgumentException
            (
                ucfirst($name) . ' must be greater than or equal to 0.'
            ) ;
        }
    }

    /**
     * Validates that an integer is strictly positive.
     *
     * @param int    $value
     * @param string $name
     *
     * @throws InvalidArgumentException
     */
    private static function assertPositive( int $value , string $name ): void
    {
        if ( $value <= 0 )
        {
            throw new InvalidArgumentException
            (
                ucfirst($name) . ' must be greater than 0.'
            ) ;
        }
    }

    /**
     * Indicates whether the ratio can safely be used
     * for proportional calculations.
     */
    private function hasValidRatio(): bool
    {
        return $this->_aspW > 0 && $this->_aspH > 0 ;
    }

    /**
     * Recalculates the simplified aspect ratio.
     *
     * Internally updates:
     *
     * - Greatest Common Divisor (GCD)
     * - Simplified width ratio
     * - Simplified height ratio
     */
    private function recalculateRatio(): void
    {
        if ( $this->_width === 0 || $this->_height === 0 )
        {
            $this->_gcd  = 1;
            $this->_aspW = $this->_width ;
            $this->_aspH = $this->_height ;

            return;
        }

        $this->_gcd = gcd
        (
            $this->_width ,
            $this->_height
        ) ?: 1;

        $this->_aspW = intdiv
        (
            $this->_width,
            $this->_gcd
        );

        $this->_aspH = intdiv
        (
            $this->_height ,
            $this->_gcd
        );
    }

    /**
     * Synchronizes the height from the current width.
     */
    private function synchronizeHeight(): void
    {
        if ( !$this->hasValidRatio() )
        {
            return;
        }

        $this->_height = (int) round
        (
            $this->_width * $this->_aspH / $this->_aspW
        );
    }

    /**
     * Synchronizes the width from the current height.
     */
    private function synchronizeWidth(): void
    {
        if ( !$this->hasValidRatio() )
        {
            return ;
        }

        $this->_width = (int) round
        (
            $this->_height * $this->_aspW / $this->_aspH
        );
    }
}
