import { Status } from "allure-js-commons";
export var AFTER_HOOKS_ROOT_STEP_TITLE = "After Hooks";
export var BEFORE_HOOKS_ROOT_STEP_TITLE = "Before Hooks";
export var statusToAllureStats = function statusToAllureStats(status, expectedStatus) {
  if (status === "skipped") {
    return Status.SKIPPED;
  }
  if (status === "timedOut") {
    return Status.BROKEN;
  }
  if (status === expectedStatus) {
    return Status.PASSED;
  }
  return Status.FAILED;
};
export var isDescendantOfStepWithTitle = function isDescendantOfStepWithTitle(step, title) {
  var parent = step.parent;
  while (parent) {
    if (parent.title === title) {
      return true;
    }
    parent = parent.parent;
  }
  return false;
};
export var isAfterHookStep = function isAfterHookStep(step) {
  return isDescendantOfStepWithTitle(step, AFTER_HOOKS_ROOT_STEP_TITLE);
};
export var isBeforeHookStep = function isBeforeHookStep(step) {
  return isDescendantOfStepWithTitle(step, BEFORE_HOOKS_ROOT_STEP_TITLE);
};
export var diffEndRegexp = /-((expected)|(diff)|(actual))\.png$/;
export var normalizeHookTitle = function normalizeHookTitle(title) {
  return title.replace(/^[aA]ttach\s"(.+)"$/, "$1");
};
export var normalizeAttachStepTitle = function normalizeAttachStepTitle(title) {
  var match = title.match(/["'`]{1}(.+?)["'`]{1}/);
  if (match !== null && match !== void 0 && match[1]) {
    return match[1];
  }
  return title.replace(/^(test\.)?attach\s*/i, "").trim();
};
//# sourceMappingURL=utils.js.map