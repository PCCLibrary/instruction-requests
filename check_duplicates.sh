#!/bin/bash

echo "=== Checking for duplicate methods in InstructionRequestService ==="
echo ""

# Extract all method names and count duplicates
grep -n "function " app/Services/InstructionRequestService.php | grep -v "// " | while read line; do
    method_name=$(echo "$line" | sed 's/.*function \([a-zA-Z_][a-zA-Z0-9_]*\).*/\1/')
    line_number=$(echo "$line" | cut -d: -f1)
    echo "Line $line_number: $method_name"
done

echo ""
echo "=== Method name frequency count ==="
grep -o "function [a-zA-Z_][a-zA-Z0-9_]*" app/Services/InstructionRequestService.php | sort | uniq -c | sort -nr

echo ""
echo "=== Checking for potential duplicates (count > 1) ==="
grep -o "function [a-zA-Z_][a-zA-Z0-9_]*" app/Services/InstructionRequestService.php | sort | uniq -c | sort -nr | awk '$1 > 1'
