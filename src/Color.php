<?php

namespace Psa\CliKit;

use BadMethodCallException;

/**
 * Class Color
 *
 * Provides a fluent interface for formatting CLI output using ANSI escape codes.
 *
 * Example:
 * echo Color::red()->bold()->underline('Tets') . PHP_EOL;
 * echo Color::blue("My blue text") . PHP_EOL;
 * echo Color::yellow()->italic("My italic") . PHP_EOL;
 * echo Color::bgRed()->white("My white color with red background") . PHP_EOL;
 *
 *
 * Text Styles:
 * @method static self|string bold(string $text = null)
 * @method self|string bold(string $text = null)
 * @method static self|string faint(string $text = null)
 * @method self|string faint(string $text = null)
 * @method static self|string italic(string $text = null)
 * @method self|string italic(string $text = null)
 * @method static self|string underline(string $text = null)
 * @method self|string underline(string $text = null)
 * @method static self|string blink(string $text = null)
 * @method self|string blink(string $text = null)
 * @method static self|string reverse(string $text = null)
 * @method self|string reverse(string $text = null)
 * @method static self|string hidden(string $text = null)
 * @method self|string hidden(string $text = null)
 * @method static self|string strike(string $text = null)
 * @method self|string strike(string $text = null)
 *
 * Foreground Colors:
 * @method static self|string black(string $text = null)
 * @method self|string black(string $text = null)
 * @method static self|string red(string $text = null)
 * @method self|string red(string $text = null)
 * @method static self|string green(string $text = null)
 * @method self|string green(string $text = null)
 * @method static self|string yellow(string $text = null)
 * @method self|string yellow(string $text = null)
 * @method static self|string blue(string $text = null)
 * @method self|string blue(string $text = null)
 * @method static self|string magenta(string $text = null)
 * @method self|string magenta(string $text = null)
 * @method static self|string cyan(string $text = null)
 * @method self|string cyan(string $text = null)
 * @method static self|string white(string $text = null)
 * @method self|string white(string $text = null)
 * @method static self|string default(string $text = null)
 * @method self|string default(string $text = null)
 *
 * Background Colors:
 * @method static self|string bgBlack(string $text = null)
 * @method self|string bgBlack(string $text = null)
 * @method static self|string bgRed(string $text = null)
 * @method self|string bgRed(string $text = null)
 * @method static self|string bgGreen(string $text = null)
 * @method self|string bgGreen(string $text = null)
 * @method static self|string bgYellow(string $text = null)
 * @method self|string bgYellow(string $text = null)
 * @method static self|string bgBlue(string $text = null)
 * @method self|string bgBlue(string $text = null)
 * @method static self|string bgMagenta(string $text = null)
 * @method self|string bgMagenta(string $text = null)
 * @method static self|string bgCyan(string $text = null)
 * @method self|string bgCyan(string $text = null)
 * @method static self|string bgWhite(string $text = null)
 * @method self|string bgWhite(string $text = null)
 * @method static self|string bgDefault(string $text = null)
 * @method self|string bgDefault(string $text = null)
 */
class Color
{
    /**
     * @var array<int> Accumulated ANSI style codes.
     */
    private array $codes = [];

    /**
     * Map of available ANSI escape codes.
     */
    private const STYLES = [
        // Text styles
        'bold'      => 1,
        'faint'     => 2,
        'italic'    => 3,
        'underline' => 4,
        'blink'     => 5,
        'reverse'   => 7,
        'hidden'    => 8,
        'strike'    => 9,

        // Foreground colors
        'black'     => 30,
        'red'       => 31,
        'green'     => 32,
        'yellow'    => 33,
        'blue'      => 34,
        'magenta'   => 35,
        'cyan'      => 36,
        'white'     => 37,
        'default'   => 39,

        // Background colors
        'bgBlack'   => 40,
        'bgRed'     => 41,
        'bgGreen'   => 42,
        'bgYellow'  => 43,
        'bgBlue'    => 44,
        'bgMagenta' => 45,
        'bgCyan'    => 46,
        'bgWhite'   => 47,
        'bgDefault' => 49,
    ];

    /**
     * Private constructor to prevent direct instantiation.
     * The object should be created via static method calls.
     */
    private function __construct()
    {
    }

    /**
     * Handles static method calls to initiate the fluent chain.
     *
     * @param string $name      The name of the style/color method.
     * @param array  $arguments The arguments passed to the method.
     *
     * @return self|string
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $instance = new self();

        return $instance->$name(...$arguments);
    }

    /**
     * Handles instance method calls for fluent chaining.
     *
     * @param string $name      The name of the style/color method.
     * @param array  $arguments The arguments passed to the method.
     *
     * @return self|string Returns a string if text is provided, otherwise returns the instance ($this).
     * @throws BadMethodCallException If the requested style is not supported.
     */
    public function __call(string $name, array $arguments)
    {
        if (!array_key_exists($name, self::STYLES)) {
            throw new BadMethodCallException("Style '{$name}' is not supported.");
        }

        // Add the corresponding ANSI code to the current instance
        $this->codes[] = self::STYLES[$name];

        // If text is provided as the first argument, apply styles and return the string
        if (isset($arguments[0])) {
            return $this->apply((string) $arguments[0]);
        }

        // Otherwise, return the instance for method chaining
        return $this;
    }

    /**
     * Wraps the given text in the accumulated ANSI escape codes.
     *
     * @param string $text The text to format.
     *
     * @return string The formatted text.
     */
    private function apply(string $text): string
    {
        // Return plain text if no styles were applied
        if (empty($this->codes)) {
            return $text;
        }

        // Disable colors if output is not a TTY or if NO_COLOR environment variable is set
        if (!stream_isatty(STDOUT) || getenv('NO_COLOR')) {
            return $text;
        }

        $codesString = implode(';', $this->codes);

        return "\e[{$codesString}m{$text}\e[0m";
    }
}
