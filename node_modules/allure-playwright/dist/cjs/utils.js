"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.statusToAllureStats = exports.normalizeHookTitle = exports.normalizeAttachStepTitle = exports.isDescendantOfStepWithTitle = exports.isBeforeHookStep = exports.isAfterHookStep = exports.diffEndRegexp = exports.BEFORE_HOOKS_ROOT_STEP_TITLE = exports.AFTER_HOOKS_ROOT_STEP_TITLE = void 0;
var _allureJsCommons = require("allure-js-commons");
var AFTER_HOOKS_ROOT_STEP_TITLE = exports.AFTER_HOOKS_ROOT_STEP_TITLE = "After Hooks";
var BEFORE_HOOKS_ROOT_STEP_TITLE = exports.BEFORE_HOOKS_ROOT_STEP_TITLE = "Before Hooks";
var statusToAllureStats = exports.statusToAllureStats = function statusToAllureStats(status, expectedStatus) {
  if (status === "skipped") {
    return _allureJsCommons.Status.SKIPPED;
  }
  if (status === "timedOut") {
    return _allureJsCommons.Status.BROKEN;
  }
  if (status === expectedStatus) {
    return _allureJsCommons.Status.PASSED;
  }
  return _allureJsCommons.Status.FAILED;
};
var isDescendantOfStepWithTitle = exports.isDescendantOfStepWithTitle = function isDescendantOfStepWithTitle(step, title) {
  var parent = step.parent;
  while (parent) {
    if (parent.title === title) {
      return true;
    }
    parent = parent.parent;
  }
  return false;
};
var isAfterHookStep = exports.isAfterHookStep = function isAfterHookStep(step) {
  return isDescendantOfStepWithTitle(step, AFTER_HOOKS_ROOT_STEP_TITLE);
};
var isBeforeHookStep = exports.isBeforeHookStep = function isBeforeHookStep(step) {
  return isDescendantOfStepWithTitle(step, BEFORE_HOOKS_ROOT_STEP_TITLE);
};
var diffEndRegexp = exports.diffEndRegexp = /-((expected)|(diff)|(actual))\.png$/;
var normalizeHookTitle = exports.normalizeHookTitle = function normalizeHookTitle(title) {
  return title.replace(/^[aA]ttach\s"(.+)"$/, "$1");
};
var normalizeAttachStepTitle = exports.normalizeAttachStepTitle = function normalizeAttachStepTitle(title) {
  var match = title.match(/["'`]{1}(.+?)["'`]{1}/);
  if (match !== null && match !== void 0 && match[1]) {
    return match[1];
  }
  return title.replace(/^(test\.)?attach\s*/i, "").trim();
};
//# sourceMappingURL=utils.js.map