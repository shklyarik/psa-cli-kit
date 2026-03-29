<?php

namespace Psa\CliKit;

/**
 * FileSelect - Provides an interactive command line file selector.
 *
 * This class creates an interactive file explorer that allows users to navigate
 * through directories and select a file. It supports directory traversal
 * (entering and leaving folders) using arrow keys or Enter.
 */
class FileSelect
{
    private string $currentDir;
    private array $items = [];
    private int $maxVisibleItems = 12; // Prevents terminal overflow

    /**
     * Constructor for FileSelect.
     *
     * @param string|null $startDirectory Directory to start in (defaults to current working directory).
     * @param string      $message        Prompt shown above the file list.
     */
    public function __construct(
        ?string $startDirectory = null,
        private string $message = 'Select a file:'
    ) {
        $this->currentDir = $startDirectory ? realpath($startDirectory) : getcwd();

        if (!$this->currentDir || !is_dir($this->currentDir)) {
            throw new \InvalidArgumentException("Invalid starting directory provided.");
        }
    }

    /**
     * Load and sort directory contents (folders first, then files).
     */
    private function loadDirectory(): void
    {
        $contents = scandir($this->currentDir);
        $folders = [];
        $files = [];

        foreach ($contents as $item) {
            // Skip current directory pointer
            if ($item === '.') continue;

            // Skip parent directory pointer if we are at the root
            if ($item === '..' && dirname($this->currentDir) === $this->currentDir) continue;

            $fullPath = $this->currentDir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($fullPath)) {
                $folders[] = $item;
            } else {
                $files[] = $item;
            }
        }

        // Sort alphabetically (case-insensitive)
        natcasesort($folders);
        natcasesort($files);

        $this->items = array_merge(array_values($folders), array_values($files));
    }

    /**
     * Render the file list with a viewport for scrolling.
     *
     * @param int $currentChoice Index of the currently highlighted item.
     * @param int $offset        Current scrolling offset.
     * @param int $previousLines How many lines to move up before redrawing.
     *
     * @return int Number of lines rendered (for the next redraw).
     */
    private function displayOptions(int $currentChoice, int $offset, int $previousLines = 0): int
    {
        // Move cursor up and clear the rest of the screen
        if ($previousLines > 0) {
            echo "\033[{$previousLines}A\033[0J";
        }

        $output = "{$this->message}\n";
        $output .= "Current Path: \033[36m{$this->currentDir}\033[0m\n";
        $output .= "--------------------------------------------------------\n";

        $linesRendered = 3;

        if (empty($this->items)) {
            echo $output . "   (Empty directory)\n";
            return $linesRendered + 1;
        }

        $totalItems = count($this->items);
        $end = min($offset + $this->maxVisibleItems, $totalItems);

        for ($i = $offset; $i < $end; $i++) {
            $item = $this->items[$i];
            $isDir = is_dir($this->currentDir . DIRECTORY_SEPARATOR . $item);

            // Add visual icons
            $icon = $item === '..' ? '🔙' : ($isDir ? '📁' : '📄');

            if ($i === $currentChoice) {
                // Highlight selected row
                $output .= " 👉 \033[44m {$icon} {$item} \033[0m\n";
            } else {
                $output .= "    {$icon} {$item}\n";
            }
            $linesRendered++;
        }

        // Show scroll indicators if needed
        $scrollMsg = "";
        if ($totalItems > $this->maxVisibleItems) {
            $scrollMsg = "   (Use Up/Down arrows to scroll. Displaying " . ($offset + 1) . "-{$end} of {$totalItems})";
        } else {
            $scrollMsg = "   (Press Enter to select file, or Right to open folder)";
        }

        $output .= "--------------------------------------------------------\n";
        $output .= "\033[90m{$scrollMsg}\033[0m\n";
        $linesRendered += 2;

        echo $output;

        return $linesRendered;
    }

    /**
     * Run the interactive file selector and return the absolute path to the chosen file.
     *
     * @return string Absolute path of the selected file.
     */
    public function select(): string
    {
        // Disable canonical mode and echo so keystrokes are captured immediately
        system('stty -icanon -echo');

        $currentChoice = 0;
        $offset = 0;
        $renderedLines = 0;

        $this->loadDirectory();

        while (true) {
            $renderedLines = $this->displayOptions($currentChoice, $offset, $renderedLines);
            $maxIndex = count($this->items) - 1;

            $char = fread(STDIN, 3);

            // Up arrow
            if ($char === "\e[A") {
                if ($currentChoice > 0) {
                    $currentChoice--;
                    if ($currentChoice < $offset) {
                        $offset--;
                    }
                }
            }
            // Down arrow
            elseif ($char === "\e[B") {
                if ($currentChoice < $maxIndex) {
                    $currentChoice++;
                    if ($currentChoice >= $offset + $this->maxVisibleItems) {
                        $offset++;
                    }
                }
            }
            // Left arrow (Go to parent directory)
            elseif ($char === "\e[D") {
                $this->currentDir = realpath($this->currentDir . '/..');
                $this->loadDirectory();
                $currentChoice = 0;
                $offset = 0;
            }
            // Right arrow or Enter
            elseif ($char === "\e[C" || $char === "\n" || $char === "\r") {
                if (empty($this->items)) {
                    continue;
                }

                $selectedItem = $this->items[$currentChoice];
                $fullPath = realpath($this->currentDir . DIRECTORY_SEPARATOR . $selectedItem);

                if (is_dir($fullPath)) {
                    // Enter directory
                    $this->currentDir = $fullPath;
                    $this->loadDirectory();
                    $currentChoice = 0;
                    $offset = 0;
                } elseif ($char === "\n" || $char === "\r") {
                    // If it's a file and Enter was pressed, we confirm the selection
                    break;
                }
            }
        }

        // Restore terminal settings
        system('stty sane');

        // Clear the menu and show the final choice
        echo "\033[{$renderedLines}A\033[0J";
        echo "{$this->message} \033[32m{$fullPath}\033[0m\n";

        return $fullPath;
    }
}
