# x-plane-map-server

## What is this?

This is the server part of the [x-plane-map](https://github.com/foucdeg/x-plane-map) plugin for X-Plane. It handles the production and display of semi-transparent tiles, as a Google Map overlay that shows X-Plane's navaids. It also stores the remote script and stylesheet for the client.

## Where are the tiles?

The images are stored in a images/ subdirectory not included here. It is about 2.5GB in size and currently holds more than 600,000 PNG tiles of 512*512.

The tiles, when requested, are either served from the images/ directory, or produced if they don't exist yet. They are produced by querying the navaid database, then using the `phpgd` library to make and save a PNG image. It takes about a second to produce a tile.

## Instructions

Just fill in `db-connect.php-example` with your database credentials and rename it to `db-connect.php`. Edit the server URL on the client page ([this file](https://github.com/foucdeg/x-plane-map/blob/master/res/index.html) lines 5 and 9) so that it points to your server.

