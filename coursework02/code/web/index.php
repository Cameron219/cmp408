<!DOCTYPE html>
<html>
    <head>
        <style>
            span {
                display: block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <span>GPIO Pin Number <input type="text" name="pin"/></span>
            <span>GPIO Pin Value
                <select name="status">
                    <option value="0" selected>0</option>
                    <option value="1">1</option>
                </select>
            </span>
            <input type="button" id="update" value="Update Status"/>
        </div>
    </body>
</html>