#!/usr/bin/env sh
set -eu

# REQ-TEST-008 contract:
# - composer.json must define:
#   - "test": "phpunit ..."
#   - "test-coverage": "phpunit ... --coverage-text ... --coverage-clover coverage.xml ..."
# - Makefile target `test-coverage` must run:
#   composer test-coverage | tee coverage-php.txt
#   ./.scripts/php-coverage-percent.sh coverage-php.txt
# - .gitignore must include /coverage-php.txt
#
# CI uses coveredelements/elements from coverage.xml (same threshold as .github/workflows/ci.yml).

RAW_FILE="${1:-coverage-php.txt}"

if [ ! -f "$RAW_FILE" ]; then
  echo "ERROR: coverage output file not found: $RAW_FILE" >&2
  exit 1
fi

# Strip ANSI color sequences if present before extracting value.
LINES_VALUE="$(
  sed 's/\x1B\[[0-9;]*[A-Za-z]//g' "$RAW_FILE" \
    | awk '/^[[:space:]]*Lines:[[:space:]]+/ { gsub(/%/, "", $2); print $2; exit }'
)"

ELEMENTS_VALUE=""
ELEMENTS_COVERED=""
if [ -f coverage.xml ]; then
  read -r ELEMENTS_COVERED ELEMENTS_VALUE <<EOF
$(php -r '
$coverage = simplexml_load_file("coverage.xml");
if ($coverage === false) { exit(1); }
$metrics = $coverage->project->metrics;
echo (float)$metrics["coveredelements"] . " " . (float)$metrics["elements"];
')
EOF
fi

if [ -z "${LINES_VALUE:-}" ] && [ -z "${ELEMENTS_VALUE:-}" ]; then
  echo "ERROR: Could not extract PHP coverage from ${RAW_FILE} or coverage.xml" >&2
  exit 1
fi

if [ -t 1 ]; then
  RED="$(printf '\033[31m')"
  ORANGE="$(printf '\033[38;5;208m')"
  GREEN="$(printf '\033[32m')"
  RESET="$(printf '\033[0m')"
else
  RED=""
  ORANGE=""
  GREEN=""
  RESET=""
fi

color_for_value() {
  _value="$1"
  if awk "BEGIN { exit !(${_value} < 50) }"; then
    printf '%s' "$RED"
  elif awk "BEGIN { exit !(${_value} <= 85) }"; then
    printf '%s' "$ORANGE"
  else
    printf '%s' "$GREEN"
  fi
}

if [ -n "${LINES_VALUE:-}" ]; then
  COLOR="$(color_for_value "$LINES_VALUE")"
  printf 'Global PHP coverage (Lines): %s%s%%%s\n' "$COLOR" "$LINES_VALUE" "$RESET"
fi

if [ -n "${ELEMENTS_VALUE:-}" ] && [ "${ELEMENTS_VALUE}" != "0" ]; then
  ELEMENTS_PCT="$(php -r "echo round(${ELEMENTS_COVERED} / ${ELEMENTS_VALUE} * 100, 2);")"
  COLOR="$(color_for_value "$ELEMENTS_PCT")"
  printf 'Global PHP coverage (Elements, CI): %s%s%%%s (%s/%s)\n' "$COLOR" "$ELEMENTS_PCT" "$RESET" "$ELEMENTS_COVERED" "$ELEMENTS_VALUE"
fi
