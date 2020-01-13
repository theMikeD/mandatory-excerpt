// This is so we can use relative paths
var path = require('path');

module.exports = {
	// Take js/src/mandatory-excerpt.js and compile it into js/mandatory-excerpt.min.js
	entry: path.resolve(__dirname, "js") + "/src/mandatory-excerpt.js",
	output: {
		filename: 'mandatory-excerpt.min.js',
		path: path.resolve(__dirname, "js"),
    },
};
