# -*- coding: utf-8-*-

"""
Prepares the workspace for commit. Fixes all common formatting errors.

Usage:
Simply run this script before you commit your changes to the repository.

Details, questions, feature requests:
https://code.google.com/p/opencaching-pl/issues/detail?id=23
"""

import os
import sys
import re
import mimetypes
import platform
import subprocess


_errors_found = False

EXIFTOOL_PATHS_TO_TRY = [
    "exiftool",
    "D:\\Standalone\\exiftool.exe",
]
SKIPPED_PATHS = [
]

_skipped_paths_absolute = None  # dynamic
_images_to_be_fixed = []


def fix_unix(buf):
    """
    Fix line delimiters in the string (should be UNIX), return the fixed
    string.
    """
    return buf.replace("\r\n", "\n")


_trailing_whitespace_regex = re.compile(r"[ \t]+$", re.MULTILINE)


def fix_trailing_whitespace(buf):
    """
    Remove all trailing whitespace in the given string, return the fixed
    string.
    """
    return _trailing_whitespace_regex.sub("", buf)


_indent_regex = re.compile(r"^[ \t]+(?!\*)", re.MULTILINE)


_lines_with_tabs_regex = re.compile(r"^.*\t.*$", re.MULTILINE)


def fix_tabs(buf, path):
    """
    Fix all tabs in the given string (replace them with proper* amount of
    spaces). Return the fixed string. The path should point to the file, but
    the file itself will NOT be modified.

    * - the meaning of "proper" demo: http://i.imgur.com/P6Q7uUR.png
    """
    apply_for = [
        ".php", ".php3", ".inc", ".js", ".html", ".tpl", ".java", ".css",
        ".in", ".py", ".cpp", ".bat", ".h", ".tmpl", ".rst", ".xml", ".sh",
        ".sql", ".xsd", ".txt", ".htm", ".c", ".dtd", ".php.old", ".svg",
        ".wlx", ".php.example", ".wsgi"
    ]
    skip = True
    for ending in apply_for:
        if path.lower().endswith(ending):
            skip = False
            break
    if skip:
        return buf

    def fix_line(line):
        mod = 0
        out = ""
        for c in line.group():
            if c == "\t":
                out += (" " * (4 - mod))
                mod = 0
            else:
                out += c
                mod = (mod + 1) % 4
        return out
    return _lines_with_tabs_regex.sub(fix_line, buf)


def check_if_binary(path, buf):
    """
    Check if the given file/string is binary. The string should match the
    contents of the file at the path.
    """
    #
    # First, just check the file extension.
    #
    binary = [
        ".phar", ".z", ".map", ".ico", ".dll", ".exe", ".gz", ".ini", ".log",
        ".zip", ".vsd", ".pyc", ".po", ".mo", ".dat", ".psd",
        ".email", ".mo", ".po", ".patch", ".swf", ".mp3", ".gz", ".ttf", ".7z",
    ]
    non_binary = [
        ".php", ".inc", ".tpl", ".cmd", ".rst", ".xml.in", ".sh.in", ".py.in",
        ".json", ".cpp", ".sql", ".tmpl", ".xsd", ".wsgi", ".wsgi.in",
        ".txt", ".txt.old",
        ".php.example", ".as", ".svg", ".kml", ".php.old", ".dtd"
    ]
    for ext in binary:
        if path.endswith(ext):
            return True
    for ext in non_binary:
        if path.endswith(ext):
            return False
    #
    # If this fails, use the mimetype module.
    #
    mimetype = mimetypes.guess_type(path)
    if mimetype[0] != None:
        t = mimetype[0]
        if t.startswith("text/"):
            return False  # non-binary
        if t.startswith("image/"):
            return True
        if t in [
            "application/x-tar", "application/pdf", "application/postscript",
            "application/octet-steam"
        ]:
            return True
        if t in ["application/x-javascript", "application/x-sh"]:
            return False
    #
    # Finally, resolve to content heuristics.
    #
    if len(buf) == 0:
        return False
    ascii = 0
    for c in buf:
        if (ord(c) < 128 and ord(c) >= 32) or c in "\t\r\nąćęłńóśźżĄĆĘŁŃÓŚŹŻ":
            ascii += 1
    return (1.0 * ascii / len(buf)) < 0.8


def fix_file(path):
    """
    Read the file at the given path, apply all fixes on it and save the result
    (overwriting the original file).
    """
    #
    # Skip hidden files and directories (like ".git" or IDE's ".settings").
    #
    path = path.replace("\\", "/")
    if "\\." in path or "/." in path:
        return
    #
    # Skip external libraries.
    #
    if any(path.startswith(x) for x in _skipped_paths_absolute):
        return
    #
    # Images
    #
    image_exts = [
        ".jpg", ".jpeg", ".png", ".gif"
    ]
    if any(path.endswith(ext) for ext in image_exts):
        _images_to_be_fixed.append(path)
        return
    #
    # Read and analyze file contents.
    #
    f = open(path, 'rb')
    buf = ""
    while True:
        chunk = f.read()
        if (chunk == ''):
            break
        buf += chunk
    f.close()
    prev_content = buf
    is_binary = check_if_binary(path, buf)
    if is_binary:
        return
    try:
        buf = fix_unix(buf)
        buf = fix_trailing_whitespace(buf)
        buf = fix_tabs(buf, path)
        if prev_content != buf:
            f = open(path, "wb")
            f.write(buf)
            f.close()
    except Exception, e:
        sys.stderr.write("Err: " + path + " - " + e.message + "\n")
        _errors_found = True


def make_groups(count, source):
    """Podziel elementy z iteratora source na grupy o maksymalnej wielkości count
    (ostatni element podziału może mieć mniej wartości niż count). Metoda działa
    podobnie do make_tuples, lecz zwraca listy (nie krotki) i nie kończy się błędem,
    w przypadku, gdy len(source) nie jest podzielne przez count. Przydaje się,
    gdy chcemy skorzystać z metody, która nakłada limit na ilość jednocześnie
    przetwarzanych argumentów (dzielimy argumenty na grupy wielkości niewiększej
    niż limit)."""
    it = iter(source)
    try:
        while True:
            group = []
            for _ in xrange(0, count):
                group.append(it.next())
            yield group
    except StopIteration:
        if len(group) > 0:
            yield group


def fix_images(filepaths):
    """Run exiftool and remove all unwanted metadata."""
    success = False
    for group in make_groups(100, filepaths):
        for path in EXIFTOOL_PATHS_TO_TRY:
            try:
                retcode = subprocess.call([path, "-all="] + group)
                if retcode == 0:
                    success = True
                    break
            except Exception, e:
                print str(e)
    if not success:
        _errors_found = True


def fix_workspace(folder):
    """Find and fix all files in the given workspace."""
    global _skipped_paths_absolute
    _skipped_paths_absolute = [
        os.path.join(folder, x).replace("\\", "/") for x in SKIPPED_PATHS
    ]
    for dirname, dirnames, filenames in os.walk(folder):
        for filename in filenames:
            fix_file(os.path.join(dirname, filename))
    fix_images(_images_to_be_fixed)


if __name__ == "__main__":
    _errors_found = False
    if len(sys.argv) == 1:
        filepath = os.path.realpath(__file__)
        ci_path = os.path.dirname(filepath)
        folder = os.path.dirname(ci_path)
        print "You are about to run commitFixer on: %s" % folder
        print "Supply this path in the argument in order to skip this warning."
        if raw_input("Continue (y/n)? ") != "y":
            sys.exit(0)
    elif len(sys.argv) == 2:
        folder = sys.argv[1]
    else:
        sys.stderr.write(
            "At most one argument expected - the path of the project you want "
            "to fix.\n"
        )
        exit(1)
    print "Fixing %s..." % folder
    fix_workspace(folder)
    if _errors_found and platform.system() == "Windows":
        #
        # Prevents the window from closing when errors are found.
        #
        raw_input("There were errors. Press ENTER to exit.")
    sys.exit(1 if _errors_found else 0)
