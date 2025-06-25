#!/bin/bash

echo "=== COMPREHENSIVE DUPLICATE METHOD CHECK ==="
echo ""

check_file() {
    local file=$1
    echo "Checking: $file"
    echo "------------------------"

    if [ ! -f "$file" ]; then
        echo "File not found: $file"
        return
    fi

    # Extract method names with line numbers
    grep -n "^\s*\(public\|private\|protected\)\s\+function\s\+" "$file" | while read line; do
        line_number=$(echo "$line" | cut -d: -f1)
        method_line=$(echo "$line" | cut -d: -f2-)
        method_name=$(echo "$method_line" | sed 's/.*function \s*\([a-zA-Z_][a-zA-Z0-9_]*\).*/\1/')
        echo "  Line $line_number: $method_name"
    done

    echo ""
    echo "Method frequency count for $file:"
    grep -o "function\s\+[a-zA-Z_][a-zA-Z0-9_]*" "$file" | sed 's/function\s\+//' | sort | uniq -c | sort -nr

    echo ""
    echo "DUPLICATES in $file (if any):"
    duplicates=$(grep -o "function\s\+[a-zA-Z_][a-zA-Z0-9_]*" "$file" | sed 's/function\s\+//' | sort | uniq -c | sort -nr | awk '$1 > 1')
    if [ -n "$duplicates" ]; then
        echo "$duplicates"
    else
        echo "No duplicates found"
    fi
    echo ""
    echo "=============================================="
    echo ""
}

# Check both files
check_file "app/Services/InstructionRequestService.php"
check_file "app/Http/Controllers/InstructionRequestController.php"
