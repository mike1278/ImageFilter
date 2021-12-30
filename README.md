# Image filter

This is a package for editing images before being returned by the server, in such a way that the original image remains.
These edits are cached.

For editing, the url parameters are used, which will be used to determine the filters to use, making it easy to use and
without restrictions.

Its current integration is solely with laravel.

## Filters available

- Resize: to edit the image size
- Colorize: to edit the image colors

### Usage example

This url resize the original image to 300 x 300

/images/{filename}?filters[resize]=300,300

This url colorize the original image

/images/{filename}?filters[colorize]=40

## Road map

- [ ] Add more filters
- [ ] Make test
- [ ] Make it usable outside laravel
