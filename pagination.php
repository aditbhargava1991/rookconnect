<?php
if(!defined('MYSQL_BOTH')) {
	define('MYSQL_BOTH',MYSQLI_BOTH);
}
if(!defined('MYSQL_NUM')) {
	define('MYSQL_NUM',MYSQLI_NUM);
}
if(!defined('MYSQL_ASSOC')) {
	define('MYSQL_ASSOC',MYSQLI_ASSOC);
}
// Number Pagination
function display_pagination($dbc, $query, $pageNum, $rowsPerPage, $allow_row_set = false, $defaultRows) {
	if(!$allow_row_set || empty($defaultRows)) {
		$defaultRows = $rowsPerPage;
	}
    $result  = mysqli_query($dbc, $query) or die('Error, query failed<!--'.$query.'-->');
    $row     = mysqli_fetch_array($result, MYSQLI_BOTH);
    $numrows = $row['numrows'];
	if(!($numrows > 0) && $numrows != 0) {
		$numrows = $row[0];
	}
	// If there are fewer rows than are displayed on a single page, just skip the pagination
	if(!($numrows > $defaultRows)) {
		return;
	}

    // how many pages we have when using paging?
    $maxPage = ceil($numrows/$rowsPerPage);

    // print the link to access each page
    //$self = $_SERVER['PHP_SELF'];
    if($_SERVER['QUERY_STRING'] == '') {
        $self = $_SERVER['PHP_SELF'];
        $permenantSelf = $self;
    } else {
        $self = basename($_SERVER['PHP_SELF']) . "?" . $_SERVER['QUERY_STRING'];
        $permenantSelf = $self;
        $self = preg_replace('/\\?page.*/', '', $self);
        $self = preg_replace('/\\&page.*/', '', $self);
    }

    $pageLower = ($pageNum - 5) > 1 ? $pageNum - 5 : 1;
    $pageLower = ($maxPage - $pageNum) < 5 ? ($maxPage > 9 ? $maxPage - 9 : 1) : $pageLower;
    $pageUpper = ($pageNum + 4) < $maxPage ? $pageNum + 4 : $maxPage;
    $pageUpper = $pageUpper > 10 ? $pageUpper : ($maxPage < 10 ? $maxPage : 10);
    $nav = '';
    if ($pageLower > 1) {
        $nav .= ' ... ';
    }
    for($page = $pageLower; $page <= $pageUpper; $page++) {
        if ($page == $pageNum) {
            $nav .= "Page $page";   // no need to create a link to current page
        }
        else {
            if($_SERVER['QUERY_STRING'] == '' || strpos($permenantSelf, '?page') !== false) {
                $nav .= " <a href=\"$self?page=$page\">$page</a> ";
            }
            else {
                $nav .= " <a href=\"$self&page=$page\">$page</a> ";
            }
        }
    }
    if ($pageUpper < $maxPage) {
        $nav .= ' ... ';
    }

    // creating previous and next link
    // plus the link to go straight to
    // the first and last page

    if ($pageNum > 1) {
        $page = $pageNum - 1;
        if($_SERVER['QUERY_STRING'] == ''  || strpos($permenantSelf, '?page') !== false) {
            $prev = " <a href=\"$self?page=$page\">[Prev]</a> ";
            $first = " <a href=\"$self?page=1\">[First Page]</a> ";
        } else {
            $prev = " <a href=\"$self&page=$page\">[Prev]</a> ";
            $first = " <a href=\"$self&page=1\">[First Page]</a> ";
        }
    } else {
        $prev  = '&nbsp;'; // we're on page one, don't print previous link
        $first = '&nbsp;'; // nor the first page link
    }

    if ($pageNum < $maxPage) {
        $page = $pageNum + 1;
        if($_SERVER['QUERY_STRING'] == '' || strpos($permenantSelf, '?page') !== false) {
            $next = " <a href=\"$self?page=$page\">[Next]</a> ";
            $last = " <a href=\"$self?page=$maxPage\">[Last Page]</a> ";
        } else {
            $next = " <a href=\"$self&page=$page\">[Next]</a> ";
            $last = " <a href=\"$self&page=$maxPage\">[Last Page]</a> ";
        }
    } else {
        $next = '&nbsp;'; // we're on the last page, don't print next link
        $last = '&nbsp;'; // nor the last page link
    }
	
	// Allow the user to select the number of rows to display per page
	$row_set = '';
	if($allow_row_set) {
        if(empty($_SERVER['QUERY_STRING'])) {
			$row_set = 'Rows: <select placeholder="Rows Per Page" onchange="window.location=\'?pagerows=\'+this.value">';
        } else {
			parse_str($_SERVER['QUERY_STRING'],$query);
			unset($query['pagerows']);
			$row_set = 'Rows: <select placeholder="Rows Per Page" onchange="window.location=\'?'.http_build_query($query).'&pagerows=\'+this.value">';
        }
		for($i = $defaultRows; $i < $numrows + $defaultRows; $i += $defaultRows) {
			$row_set .= "<option ".($rowsPerPage == $i ? 'selected' : '')." value=$i>".($i > $numrows ? $numrows : $i)."</option>";
		}
		$row_set .= '</select>';
	}

    // print the navigation link
    echo "<br /><div class='gap-top gap-bottom pagination'>".$first . $prev . $nav . $next . $last.$row_set."</div><br />";

} ?>
