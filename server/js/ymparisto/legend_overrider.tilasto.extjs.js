//Legendan override funktio. Rivitt‰‰ selitteet.
Ext.override(Ext.chart.Legend, {
createItems: function() {
var me = this,
chart = me.chart,
surface = chart.surface,
items = me.items,
padding = me.padding,
itemSpacing = me.itemSpacing,
spacingOffset = 2,
itemWidth = 0,
itemHeight = 0,
totalWidth = 0,
totalHeight = 0,
vertical = me.isVertical,
math = Math,
mfloor = math.floor,
mmax = math.max,
index = 0,
i = 0,
len = items ? items.length : 0,
x, y, item, bbox, height, width,
// chart dimensions
chartBBox = chart.chartBBox,
chartInsets = chart.insetPadding,
chartWidth = chartBBox.width - (chartInsets * 2),
chartHeight = chartBBox.height - (chartInsets * 2),
xOffset = 0, yOffset = 0,
legendWidth = 0, legendHeight = 0,
legendXOffset = 50, legendYOffset = 50,
hSpacing, vSpacing;

//remove all legend items
if (len) {
for (; i < len; i++) {
items[i].destroy();
}
}
//empty array
items.length = [];

// Create all the item labels, collecting their dimensions and positioning each one
// properly in relation to the previous item
chart.series.each(function(series, i) {
if (series.showInLegend) {
Ext.each([].concat(series.yField), function(field, j) {
item = new Ext.chart.LegendItem({
legend: this,
series: series,
surface: chart.surface,
yFieldIndex: j
});
bbox = item.getBBox();

width = bbox.width;
height = bbox.height;

itemWidth = mmax(itemWidth, width);
itemHeight = mmax(itemHeight, height);

items.push(item);
}, this);

}
}, me);

//spacing = itemSpacing / (vertical ? 2 : 1);
vSpacing = itemSpacing / 2;
hSpacing = itemSpacing;
if (vertical) {
if ( chartHeight - legendYOffset < items.length * (itemHeight + vSpacing) + 2 * padding + vSpacing) {
legendHeight = chartHeight - legendYOffset;
yOffset = mfloor((legendHeight - mfloor((legendHeight - 2 * padding - vSpacing) / (itemHeight + vSpacing)) * (itemHeight + vSpacing) ) / 2);
}
else {
legendHeight = items.length * (itemHeight + vSpacing) + 2 * padding + vSpacing;
yOffset = vSpacing + padding;
}
xOffset = hSpacing + padding;
totalWidth = xOffset;
totalHeight = yOffset;
}
else {
if ( chartWidth - legendXOffset < items.length * (itemWidth + hSpacing) + 2 * padding + hSpacing) {
legendWidth = chartWidth - legendXOffset;
xOffset = mfloor((legendWidth - mfloor((legendWidth - 2 * padding - hSpacing) / (itemWidth + hSpacing)) * (itemWidth + hSpacing) ) / 2);
}
else {
legendWidth = items.length * (itemWidth + hSpacing) + 2 * padding + hSpacing;
xOffset = padding + hSpacing;
}
yOffset = padding + vSpacing;
totalHeight = yOffset;
totalWidth = xOffset;
}

Ext.each(items, function(item, j) {
if (vertical && (totalHeight + vSpacing + itemHeight > chartHeight - legendYOffset)) {
totalHeight = yOffset;
totalWidth += itemWidth + hSpacing;
}
else if (!vertical && (totalWidth + hSpacing + itemWidth > chartWidth - legendXOffset)) {
totalWidth = xOffset;
totalHeight += itemHeight + vSpacing;
}
item.x = totalWidth;
item.y = mfloor(totalHeight + itemHeight / 2);

// Collect cumulative dimensions
if (vertical)
totalHeight += itemHeight + vSpacing;
else
totalWidth += itemWidth + hSpacing;

}, me);

// Store the collected dimensions for later
me.width = mfloor(vertical ? totalWidth + itemWidth + xOffset : legendWidth);
me.height = mfloor(vertical ? legendHeight : totalHeight + itemHeight + yOffset );
me.itemHeight = itemHeight;
me.itemWidth = itemWidth;
}

});
