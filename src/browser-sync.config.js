module.exports = {
	proxy: "http://localhost/aikota/",
	notify: false,
	files: ["build/css/*.min.css", "build/js/*.min.js", "**/*.php"],
};