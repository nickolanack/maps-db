/**
 * File - SimpleKml.js
 * Package - Geolive. http://www.geolive.ca
 * Created - Dec 18 09
 * Author - Nick Blackwell
 * 
 * License - Geolive by Nicholas Blackwell is licensed under a Creative Commons Attribution-NoDerivs 3.0 Unported License.
 *
 * Description - Defines a class, SimpleParser which is a container for static kml parsing methods. 
 * This kml parser assumes Mootools and GoogleMaps API are available.
 * 
 * Dependencies - GoogleMaps API, Mootools 1.11, JSConsole.js (specifically JSConsole method)
 */

/**
 * SimpleParser Class parses standard kml documents and returns objects representiong it's data. 
 * the optional transformations define the data within these objects, ie, documentTransform (for Geolive)
 * will create a Layer object from its contents, and pull out the items which will be transformed aswell as MapItems.
 * 
 * 
 * 
 */
var SimpleParser = new Class({
    options: {
        /** markerParams=[name, description, coordinates, icon, data]; xmlSnippet=xmlNode*/
        markerTransform: function(markerParams, xmlSnippet) {
            return markerParams;
        },
        /** polygonParams=[name, description, coordinates, lineWidth, lineColor, lineOpacity, fillColor, fillOpacity]; xmlSnippet=xmlNode*/
        polygonTransform: function(polygonParams, xmlSnippet) {
            return polygonParams;
        },
        /** lineParams=[name, description, coordinates, lineWidth, lineColor, lineOpacity]; xmlSnippet=xmlNode*/
        lineTransform: function(lineParams, xmlSnippet) {
            return lineParams;
        },
        groundOverlayTransform: function(overlayParams, xmlSnippet) {
            return overlayParams;
        },
        /** folderParams=[name, description, markers, polygons, lines, networklinks, data]; xmlSnippet=xmlNode*/
        folderTransform: function(folderParams, xmlSnippet) {
            return folderParams;
        },
        /** networklinkParams=[name, description, url, data]; xmlSnippet=xmlNode*/
        networklinkTransform: function(networklinkParams, xmlSnippet) {

            return networklinkParams;
        },
        /** documentParams=[name, description, folders, markers, polygons, lines, networklinks, data]; xmlSnippet=xmlNode*/
        documentTransform: function(documentParams, xmlSnippet) {

            return documentParams;
        }
    },
    initialize: function(options) {

        if (!window.JSConsoleWarn) {
            window.JSConsoleWarn = function() {};
        }
        if (!window.JSConsoleError) {
            window.JSConsoleError = function() {};
        }


        var me = this;
        me.setOptions(options);
    },
    parseDocuments: function(snippet) {
        var me = this;
        var documents = [];
        var documentData = me._filter(SimpleParser.ParseDomDocuments(snippet));
        Array.each(documentData, function(p, i) {
            documents.push(me.options.documentTransform(p, snippet, documentData, i));
        });
        return documents;
    },
    parseFolders: function(snippet) {
        var me = this;
        var folders = [];
        var folderData = me._filter(SimpleParser.ParseDomFolders(snippet));
        Array.each(folderData, function(p, i) {
            folders.push(me.options.folderTransform(p, snippet, folderData, i));
        });
        return folders;
    },
    parseMarkers: function(snippet) {
        var me = this;
        var markers = [];
        var markerData = me._filter(SimpleParser.ParseDomMarkers(snippet));
        Array.each(markerData, function(p, i) {
            markers.push(me.options.markerTransform(p, snippet, markerData, i));
        });
        return markers;
    },
    parsePolygons: function(snippet) {
        var me = this;
        var polygons = [];

        var polygonData = me._filter(SimpleParser.ParseDomPolygons(snippet));
        Array.each(polygonData, function(p, i) {
            polygons.push(me.options.polygonTransform(p, snippet, polygonData, i));
        });
        return polygons;
    },
    parseLines: function(snippet) {
        var me = this;
        var lines = [];
        var lineData = me._filter(SimpleParser.ParseDomLines(snippet));
        Array.each(lineData, function(p, i) {
            lines.push(me.options.lineTransform(p, snippet, lineData, i));
        });
        return lines;
    },
    parseGroundOverlays: function(snippet) {
        var me = this;
        var groundOverlays = [];
        var overlayData = me._filter(SimpleParser.ParseDomGroundOverlays(snippet));
        Array.each(overlayData, function(o, i) {
            groundOverlays.push(me.options.groundOverlayTransform(o, snippet, overlayData, i));
        });
        return groundOverlays;
    },
    parseNetworklinks: function(snippet) {
        var me = this;
        var links = [];
        var linkData = me._filter(SimpleParser.ParseDomLinks(snippet));
        Array.each(linkData, function(p, i) {
            links.push(me.options.networklinkTransform(p, snippet, linkData, i));
        });
        return links;
    },
    _filter: function(a) {
        var me = this;
        var filtered = [];
        if (me._filters && a && a.length) {
            Array.each(a, function(item) {

                var bool = true;
                Array.each(me._filters, function(f) {
                    if (f(item) === false)
                        bool = false;
                });
                if (bool) {
                    filtered.push(item);
                }
            });
            return filtered;
        }
        return a;
    },
    addFilter: function(filter) {
        var me = this;
        if (!me._filters) {
            me._filters = [];
        }
        me._filters.push(filter);
        return filter;
    }

});

SimpleParser.implement(new Options());

SimpleParser.ParseDomDocuments = function(xmlDom) {
var docs = [];
var docsDomNodes = xmlDom.getElementsByTagName('Document');
var i;
for (i = 0; i < docsDomNodes.length; i++) {
    var node = docsDomNodes.item(i);
    var docsData = Object.merge({}, SimpleParser.ParseDomDoc(node), SimpleParser.ParseNonSpatialDomData(node, {}));
    var transform = function(options) {
        return options;
    };
    docs.push(transform(docsData));
}
return docs;
};

SimpleParser.ParseDomDoc = function(xmlDom) {
return {};
};

SimpleParser.ParseDomFolders = function(xmlDom) {
var folders = [];
var folderDomNodes = SimpleParser.ParseDomItems(xmlDom, 'Folder');
var i;
for (i = 0; i < folderDomNodes.length; i++) {
    var node = folderDomNodes[i];
    var folderData = Object.append({
        type: 'folder'
    }, SimpleParser.ParseDomFolder(node), SimpleParser.ParseNonSpatialDomData(node, {}));
    var transform = function(options) {
        return options;
    };
    folders.push(transform(folderData));
}
return folders;
};

SimpleParser.ParseDomDoc = function(xmlDom) {
return {};
};

SimpleParser.ParseDomLinks = function(xmlDom) {
var links = [];
var linkDomNodes = xmlDom.getElementsByTagName('NetworkLink');
var i;
for (i = 0; i < linkDomNodes.length; i++) {
    var node = linkDomNodes.item(i);
    var linkData = Object.merge({}, SimpleParser.ParseDomLink(node), SimpleParser.ParseNonSpatialDomData(node, {}));

    var transform = function(options) {
        return options;
    };
    links.push(transform(linkData));
}
return links;
};
SimpleParser.ParseDomFolder = function(xmlDom) {
return {};
};
SimpleParser.ParseDomLink = function(xmlDom) {

var urls = xmlDom.getElementsByTagName('href');
var link = {
    type: 'link'
};
if (urls.length > 0) {
    var url = urls.item(0);
    link.url = SimpleParser.Value(url);
}
return link;
};

SimpleParser.ParseDomLines = function(xmlDom) {
var lines = [];
var lineDomNodes = SimpleParser.ParseDomItems(xmlDom, 'LineString');
var i;
for (i = 0; i < lineDomNodes.length; i++) {

    var node = lineDomNodes[i];

    var polygonData = Object.append({
        type: 'line',
        lineColor: '#FF000000', // black
        lineWidth: 1,
        polyColor: '#77000000', //black semitransparent,
        coordinates: SimpleParser.ParseDomCoordinates(node) //returns an array of GLatLngs
    },
        Object.append(
            SimpleParser.ParseNonSpatialDomData(node, {}),
            SimpleParser.ResolveDomStyle(SimpleParser.ParseDomStyle(node), xmlDom)
        )
    );

    var rgb = SimpleParser.KMLConversions.KMLColorToRGB(polygonData.lineColor);
    polygonData.lineOpacity = rgb.opacity;
    polygonData.lineColor = rgb.color;

    lines.push(polygonData);
}

return lines;
};

SimpleParser.ParseDomGroundOverlays = function(xmlDom) {
var lines = [];
var lineDomNodes = SimpleParser.ParseDomItems(xmlDom, 'GroundOverlay');
var i;
for (i = 0; i < lineDomNodes.length; i++) {

    var node = lineDomNodes[i];

    var polygonData = Object.append(
        {
            type: 'imageoverlay',
            icon: SimpleParser.ParseDomIcon(node),
            bounds: SimpleParser.ParseDomBounds(node)
        },
        SimpleParser.ParseNonSpatialDomData(node, {})
    );

    lines.push(polygonData);
}

return lines;
};

SimpleParser.ParseDomPolygons = function(xmlDom) {
var polygons = [];
var polygonDomNodes = SimpleParser.ParseDomItems(xmlDom, 'Polygon');

var i;
for (i = 0; i < polygonDomNodes.length; i++) {

    var node = polygonDomNodes[i];

    var polygonData = Object.append({
        type: 'polygon',
        fill: true,
        lineColor: '#FF000000', // black
        lineWidth: 1,
        polyColor: '#77000000', //black semitransparent,
        coordinates: SimpleParser.ParseDomCoordinates(node) //returns an array of google.maps.LatLng
    },
        Object.append(
            SimpleParser.ParseNonSpatialDomData(node, {}),
            SimpleParser.ResolveDomStyle(SimpleParser.ParseDomStyle(node), xmlDom)
        )
    );

    var lineRGB = SimpleParser.KMLConversions.KMLColorToRGB(polygonData.lineColor);

    polygonData.lineOpacity = lineRGB.opacity;
    polygonData.lineColor = lineRGB.color;

    var polyRGB = SimpleParser.KMLConversions.KMLColorToRGB(polygonData.polyColor);

    polygonData.polyOpacity = (polygonData.fill) ? polyRGB.opacity : 0;
    polygonData.polyColor = polyRGB.color;


    polygons.push(polygonData);
}
return polygons;
};

SimpleParser.ParseDomMarkers = function(xmlDom) {
var markers = [];
var markerDomNodes = SimpleParser.ParseDomItems(xmlDom, 'Point');
var i;
for (i = 0; i < markerDomNodes.length; i++) {
    var node = markerDomNodes[i];
    var coords = SimpleParser.ParseDomCoordinates(node);
    var marker = Object.append({
        type: 'point'
    }, {
        coordinates: coords[0] //returns an array of google.maps.LatLng
    }, SimpleParser.ParseNonSpatialDomData(node, {}));
    var icon = SimpleParser.ParseDomStyle(node);
    if (icon.charAt(0) == '#') {
        icon = SimpleParser.ResolveDomStyle(icon, xmlDom).icon;
    }
    if (icon) {
        marker['icon'] = icon; //better to not have any hint of an icon (ie: icon:null) so that default can be used by caller
    }
    markers.push(marker);
}
return markers;
};


SimpleParser.ParseDomCoordinates = function(xmlDom) {
var coordNodes = xmlDom.getElementsByTagName('coordinates');
if (coordNodes.length == 0) {
    JSConsoleWarn(["SimpleParser. DOM Node did not contain coordinates!", {
        node: xmlDom
    }]);
    return null;
}
var node = coordNodes.item(0);
var s = SimpleParser.Value(node);
s = s.trim();
var coordStrings = s.split(' ');
var coordinates = [];
Object.each(coordStrings, function(coord) {
    var c = coord.split(',');
    if (c.length > 1) {

        //JSConsole([c[1],c[0]]);
        coordinates.push([c[1], c[0]]);
    }

});


return coordinates;
};
SimpleParser.ParseDomBounds = function(xmlDom) {
var coordNodes = xmlDom.getElementsByTagName('LatLonBox');
if (coordNodes.length == 0) {
    JSConsoleWarn(["SimpleParser. DOM Node did not contain coordinates!", {
        node: xmlDom
    }]);
    return null;
}
var node = coordNodes.item(0);
var norths = node.getElementsByTagName('north');
var souths = node.getElementsByTagName('south');
var easts = node.getElementsByTagName('east');
var wests = node.getElementsByTagName('west');

if (norths.length == 0) {
    JSConsoleWarn(["SimpleParser. DOM LatLngBox Node did not contain north!", {
        node: xmlDom
    }]);
} else {
    var north = parseFloat(SimpleParser.Value(norths.item(0)));
}
if (souths.length == 0) {
    JSConsoleWarn(["SimpleParser. DOM LatLngBox Node did not contain south!", {
        node: xmlDom
    }]);
} else {
    var south = parseFloat(SimpleParser.Value(souths.item(0)));
}
if (easts.length == 0) {
    JSConsoleWarn(["SimpleParser. DOM LatLngBox Node did not contain east!", {
        node: xmlDom
    }]);
} else {
    var east = parseFloat(SimpleParser.Value(easts.item(0)));
}
if (wests.length == 0) {
    JSConsoleWarn(["SimpleParser. DOM LatLngBox Node did not contain west!", {
        node: xmlDom
    }]);
} else {
    var west = parseFloat(SimpleParser.Value(wests.item(0)));
}
return {
    north: north,
    south: south,
    east: east,
    west: west
};

};

SimpleParser.ParseNonSpatialDomData = function(xmlDom, options) {
var config = Object.merge({}, {
    maxOffset: 2
}, options);

var data = {
    name: "",
    description: null,
    tags: {}
};
var names = xmlDom.getElementsByTagName('name');
var i;
for (i = 0; i < names.length; i++) {
    if (SimpleParser.WithinOffsetDom(xmlDom, names.item(i), config.maxOffset)) {
        data.name = (SimpleParser.Value(names.item(i)));
        break;
    }
}
var descriptions = xmlDom.getElementsByTagName('description');
for (i = 0; i < descriptions.length; i++) {
    if (SimpleParser.WithinOffsetDom(xmlDom, descriptions.item(i), config.maxOffset)) {
        data.description = (SimpleParser.Value(descriptions.item(i)));
        break;
    }
}

if (xmlDom.hasAttribute('id')) {
    data.id = parseInt(xmlDom.getAttribute('id'), 10);
}

var tags = {};
var extendedDatas = xmlDom.getElementsByTagName('ExtendedData');
for (i = 0; i < extendedDatas.length; i++) {
    if (SimpleParser.WithinOffsetDom(xmlDom, extendedDatas.item(i), config.maxOffset)) {
        var j;
        for (j = 0; j < extendedDatas.item(i).childNodes.length; j++) {
            var c = extendedDatas.item(i).childNodes.item(j);
            var t = SimpleParser.ParseTag(c);
            if (t.name != "#text") {
                data.tags[t.name] = t.value;
            }
        }
    }
}
return data;
};

SimpleParser.ParseTag = function(xmlDom) {
var tags = {
    name: null,
    value: {}
};
switch (xmlDom.nodeName) {

case "Data": //TODO: add data tags...
case "data":
    break;
case "ID": tags.name = "ID";
    tags.value = SimpleParser.Value(xmlDom);
    break;
default:
    tags.name = xmlDom.nodeName;
    tags.value = SimpleParser.Value(xmlDom);
    break;
}
return tags;
};

SimpleParser.WithinOffsetDom = function(parent, child, max) {
var current = child.parentNode;
for (var i = 0; i < max; i++) {
    if (current.nodeName == (typeof (parent) == 'string' ? parent : parent.nodeName)) {
        return true;
    }
    current = current.parentNode;
}
JSConsoleError(["SimpleParser. Could not find parent node within expected bounds.", {
    parentNode: parent,
    childNode: child,
    bounds: max
}]);
return false;
};
SimpleParser.ParseDomStyle = function(xmlDom, options) {

var config = Object.merge({}, {
    defaultStyle: "default"
}, options);



var styles = xmlDom.getElementsByTagName('styleUrl');
var style = config.defaultStyle;
if (styles.length == 0) {
    JSConsoleWarn(["SimpleParser. DOM Node did not contain styleUrl!", {
        node: xmlDom,
        options: config
    }]);
} else {
    var node = styles.item(0);
    style = (SimpleParser.Value(node));
}
return style;
};
SimpleParser.ParseDomIcon = function(xmlDom, options) {

var config = Object.merge({}, {
    defaultIcon: false,
    defaultScale: 1.0
}, options);



var icons = xmlDom.getElementsByTagName('Icon');
var icon = config.defaultStyle;
var scale = config.defaultScale;
if (icons.length == 0) {
    JSConsoleWarn(["SimpleParser. DOM Node did not contain Icon!", {
        node: xmlDom,
        options: config
    }]);
} else {
    var node = icons.item(0);
    var urls = node.getElementsByTagName('href');
    if (urls.length == 0) {
        JSConsoleWarn(["SimpleParser. DOM Icon Node did not contain href!", {
            node: xmlDom,
            options: config
        }]);
    } else {
        var hrefNode = urls.item(0);
        icon = (SimpleParser.Value(hrefNode));
    }

    var scales = node.getElementsByTagName('viewBoundScale');
    if (scales.length == 0) {
        JSConsoleWarn(["SimpleParser. DOM Icon Node did not contain viewBoundScale!", {
            node: xmlDom,
            options: config
        }]);

    } else {
        var scaleNode = scales.item(0);
        scale = parseFloat(SimpleParser.Value(scaleNode));
    }


}
return {
    url: icon,
    scale: scale
};
};
SimpleParser.ResolveDomStyle = function(style, xmlDom) {
var data = {
};
var name = (style.charAt(0) == '#' ? style.substring(1, style.length) : style);
var styles = xmlDom.getElementsByTagName("Style");
var i;
for (i = 0; i < styles.length; i++) {

    var node = styles.item(i);
    var id = node.getAttribute("id");
    if (id == name) {
        var lineStyles = node.getElementsByTagName('LineStyle');
        var polyStyles = node.getElementsByTagName('PolyStyle');
        var iconStyles = node.getElementsByTagName('href');
        if (lineStyles.length > 0) {
            var lineStyle = lineStyles.item(0);
            var colors = lineStyle.getElementsByTagName('color');
            if (colors.length > 0) {
                var color = colors.item(0);
                data.lineColor = SimpleParser.Value(color);
            }
            var widths = lineStyle.getElementsByTagName('width');
            if (widths.length > 0) {
                var width = widths.item(0);
                data.lineWidth = SimpleParser.Value(width);
            }
        }
        if (polyStyles.length > 0) {
            var polyStyle = polyStyles.item(0);
            var colors = polyStyle.getElementsByTagName('color');
            if (colors.length > 0) {
                var color = colors.item(0);
                data.polyColor = SimpleParser.Value(color);
            }
            var outlines = polyStyle.getElementsByTagName('outline');
            if (outlines.length > 0) {
                var outline = outlines.item(0);
                var o = SimpleParser.Value(outline);
                data.outline = (o ? true : false);
            }
        }
        if (iconStyles.length > 0) {
            var iconStyle = iconStyles.item(0);
            var icon = SimpleParser.Value(iconStyle);
            data.icon = icon;
        }
    }
}
return data;
};
SimpleParser.ParseDomItems = function(xmlDom, tag) {
var tagName = tag || 'Point';
var items = [];
var markerDomNodes = xmlDom.getElementsByTagName(tagName);
var i;
for (i = 0; i < markerDomNodes.length; i++) {
    var node = markerDomNodes.item(i);
    if (tag == "GroundOverlay") {
        items.push(node);
        continue;
    }
    var parent = (node.parentNode.nodeName == 'Placemark' ? node.parentNode : (node.parentNode.parentNode.nodeName == 'Placemark' ? node.parentNode.parentNode : null));
    if (parent == null) {
        JSConsoleError(['Failed to find ParentNode for Element - ' + tagName, {
            node: xmlDom
        }]);
        mm_trace();
    } else {
        items.push(parent);
    }
}
return items;
};

SimpleParser.KMLConversions = {


    // KML Color is defined similar to RGB except it is in the opposite order and starts with opacity, 
    // #OOBBGGRR
    KMLColorToRGB: function(colorString) {
        var colorStr = colorString.replace('#', '');
        while (colorStr.length < 6) {
            colorStr = '0' + colorStr;
        } //make sure line is dark!
        while (colorStr.length < 8) {
            colorStr = 'F' + colorStr;
        } //make sure opacity is a large fraction
        if (colorStr.length > 8) {
            colorStr = colorStr.substring(0, 8);
        }
        var color = colorStr.substring(6, 8) + colorStr.substring(4, 6) + colorStr.substring(2, 4);
        var opacity = ((parseInt(colorStr.substring(0, 2), 16)) * 1.000) / (parseInt("FF", 16));

        rgbVal = {
            color: '#' + color,
            opacity: opacity
        };

        return rgbVal;
    },
    RGBColorToKML: function(rgb, opacity) {

        var colorStr = rgb.replace('#', '');
        while (colorStr.length < 6) {
            colorStr = '0' + colorStr;
        } //make sure line is dark!
        if (colorStr.length > 6) {
            colorStr = colorStr.substring(0, 6);
        }

        if( (opacity != null) ) {
            if (opacity >= 0.0 && opacity <= 1.0) {
                var opacityNum = opacity;
            } else if (parseInt(opacity) >= 0.0 && parseInt(opacity) <= 1.0) {
                var opacityNum = parseInt(opacity);
            }
        }
        if( (opacityNum == null) ) {
            var opacityNum = 1.0;
        }

        var opacityNum = (opacityNum * 255.0);
        var opacityStr = opacityNum.toString(16);

        var kmlStr = opacityStr.substring(0, 2) + "" + colorStr.substring(4, 6) + colorStr.substring(2, 4) + colorStr.substring(0, 2);

        return kmlStr;
    }

};
SimpleParser.Value = function(node) {
var value = node.nodeValue;
if (value) return value;
var str = "";
try {
    if (node.childNodes && node.childNodes.length) Object.each(SimpleParser.ChildNodesArray(node), function(c) {
            str += SimpleParser.Value(c);
        });
} catch ( e ) {
    (node.childNodes);
    JSConsoleError(['SimpleKML Parser Exception', e]);
}
return str;
};

SimpleParser.ChildNodesArray = function(node) {
var array = [];
if (node.childNodes && node.childNodes.length > 0) {
    var i = 0;
    for (i = 0; i < node.childNodes.length; i++) {
        array.push(node.childNodes.item(i));
    }

}
return array;
};


