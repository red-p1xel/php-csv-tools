<?php

const __TYPE_EXCEPTION_MESSAGE__ = 'exception';
const __TYPE_INTERNAL_ERROR_MESSAGE__ = 'error';
const __TYPE_NOTIFICATION_MESSAGE__ = 'notification';

const __UPLOADS__ = '/storage/uploads/';
const __STORAGE__ = __DIR__ . __UPLOADS__;
const __ROOT_DIR__ = __DIR__;

if (!ini_get("auto_detect_line_endings")) {
    ini_set("auto_detect_line_endings", '1');
}

const __FILE_READ_WRITE_EXCEPTION__ = "File can't be opened";

const __INVALID_FILE_FORMAT__ = "Invalid format: CSV must be initialized with heading indexes contained at the first line.";

const __TYPE_INVALID_FORMAT_BEHAVIOR__ = 'resolve';

$dump = 'Invalid heading indexes. Please resolve CVS heading indexes and try again.';