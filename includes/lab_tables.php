<?php

/**
 * Shared HTML builders for the LMT sync tables in log.php.
 * Column lists and colours match the previous per-table copies.
 */

/**
 * @param string $name
 * @return array<int, mixed>
 */
function labTableSpec($name)
{
    $tables = array(
        "assetReservation" => array(
            "assetreservationentry",
            6,
            "#007700",
            true,
            false,
            array("count", 0, 2, 3, 7, 8),
        ),
        "crbReservation" => array(
            "crbreservationentry",
            6,
            "#007700",
            true,
            false,
            array("count", 0, 2, 3, 7, 9),
        ),
        "asset" => array(
            "assetentry",
            6,
            "#007700",
            false,
            false,
            array("count", 0, 2, 3, 7, 8),
        ),
        "crb" => array(
            "crbentry",
            6,
            "#007700",
            false,
            false,
            array("count", 0, 2, 3, 7, 9),
        ),
        "assetReservationError" => array(
            "assetreservation",
            7,
            "#770000",
            false,
            true,
            array("count", 0, 2, "error", 3, 4, 5, 7, 8, 9),
        ),
        "crbReservationError" => array(
            "crbreservation",
            7,
            "#770000",
            false,
            true,
            array("count", 0, "error", 3, 4, 9),
        ),
        "assetError" => array(
            "asset",
            7,
            "#770000",
            false,
            true,
            array("count", 0, 2, "error", 3, 8, 9),
        ),
        "crbError" => array(
            "crb",
            7,
            "#770000",
            false,
            true,
            array("count", 0, "error", 3, 4, 5, 7, 9, 10),
        ),
    );

    return $tables[$name];
}

/**
 * @param int $labIndex
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @param string $entryType
 * @param int $locationIndex
 * @param string $color
 * @param bool $brokenStyle
 * @param bool $isError
 * @param array<int, int|string> $columns
 * @return string
 */
function renderLabTable(
    $labIndex,
    &$syncdata,
    &$contents,
    $location,
    $entryType,
    $locationIndex,
    $color,
    $brokenStyle,
    $isError,
    $columns
) {
    $count = 1;
    $table = "<table style='border-top: 1px dashed " . $color
        . ";border-bottom: 1px dashed " . $color . "; width:100%'>";
    $error = null;

    foreach ($contents as $line) {
        if (stristr($line, "|") !== false) {
            $data = explode("|", $line);

            if ($data[1] == $entryType && $location == $data[$locationIndex]) {
                if ($isError) {
                    if ($data[2] == "macerror") {
                        $syncdata[$labIndex][3] += 1;
                        $error = "InvalidMAC";
                    } elseif ($data[2] == "iperror") {
                        $syncdata[$labIndex][4] += 1;
                        $error = "InvalidIP";
                    } elseif ($data[2] == "iperrorvlan") {
                        $syncdata[$labIndex][5] += 1;
                        $error = "WrongVLAN";
                    }
                } else {
                    $syncdata[$labIndex][1] += 1;
                    $syncdata[$labIndex][2] += 1;
                }

                if ($brokenStyle) {
                    $rowcolor = ($count % 2 == 0)
                        ? "style='background-color:#eeeeee; style='color:" . $color . "''"
                        : "style='background-color:#ffffff; style='color:" . $color . "''";
                } else {
                    $rowcolor = ($count % 2 == 0)
                        ? "style='background-color:#eeeeee;color:" . $color . "'"
                        : "style='background-color:#ffffff;color:" . $color . "'";
                }

                $table .= "<tr>";
                foreach ($columns as $column) {
                    if ($column === "count") {
                        $table .= "<td $rowcolor class='padme2'><b>" . $count . "</b></td>";
                    } elseif ($column === "error") {
                        $table .= "<td $rowcolor class='padme2'>" . $error . "</td>";
                    } else {
                        $table .= "<td $rowcolor class='padme2'>" . $data[$column] . "</td>";
                    }
                }
                $table .= "</tr>";

                $count++;
            }
        }
    }

    $table .= "</table>";

    return $table;
}

/**
 * @param string $name
 * @param int $labIndex
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function labTable($name, $labIndex, &$syncdata, &$contents, $location)
{
    $spec = labTableSpec($name);

    return renderLabTable(
        $labIndex,
        $syncdata,
        $contents,
        $location,
        $spec[0],
        $spec[1],
        $spec[2],
        $spec[3],
        $spec[4],
        $spec[5]
    );
}

/**
 * @param int $lab_index
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function getassetReservationTable($lab_index, &$syncdata, &$contents, $location)
{
    return labTable("assetReservation", $lab_index, $syncdata, $contents, $location);
}

/**
 * @param int $lab_index
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function getcrbReservationTable($lab_index, &$syncdata, &$contents, $location)
{
    return labTable("crbReservation", $lab_index, $syncdata, $contents, $location);
}

/**
 * @param int $lab_index
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function getassetReservationErrorTable($lab_index, &$syncdata, &$contents, $location)
{
    return labTable("assetReservationError", $lab_index, $syncdata, $contents, $location);
}

/**
 * @param int $lab_index
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function getcrbReservationErrorTable($lab_index, &$syncdata, &$contents, $location)
{
    return labTable("crbReservationError", $lab_index, $syncdata, $contents, $location);
}

/**
 * @param int $lab_index
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function getAssetTable($lab_index, &$syncdata, &$contents, $location)
{
    return labTable("asset", $lab_index, $syncdata, $contents, $location);
}

/**
 * @param int $lab_index
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function getCrbTable($lab_index, &$syncdata, &$contents, $location)
{
    return labTable("crb", $lab_index, $syncdata, $contents, $location);
}

/**
 * @param int $lab_index
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function getAssetErrorTable($lab_index, &$syncdata, &$contents, $location)
{
    return labTable("assetError", $lab_index, $syncdata, $contents, $location);
}

/**
 * @param int $lab_index
 * @param array<int, array<int, mixed>> $syncdata
 * @param array<int, string> $contents
 * @param string $location
 * @return string
 */
function getCrbErrorTable($lab_index, &$syncdata, &$contents, $location)
{
    return labTable("crbError", $lab_index, $syncdata, $contents, $location);
}
