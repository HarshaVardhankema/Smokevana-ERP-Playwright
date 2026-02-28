function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { if (r) i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n;else { var o = function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); }; o("next", 0), o("throw", 1), o("return", 2); } }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t["return"] || t["return"](); } finally { if (u) throw o; } } }; }
function _toArray(r) { return _arrayWithHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _classPrivateMethodInitSpec(e, a) { _checkPrivateRedeclaration(e, a), a.add(e); }
function _checkPrivateRedeclaration(e, t) { if (t.has(e)) throw new TypeError("Cannot initialize the same private elements twice on an object"); }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _assertClassBrand(e, t, n) { if ("function" == typeof e ? e === t : e.has(t)) return arguments.length < 3 ? t : n; throw new TypeError("Private element is not present on this object"); }
/* eslint max-lines: off */

import { existsSync } from "node:fs";
import { access } from "node:fs/promises";
import path from "node:path";
import process from "node:process";
import { ContentType, LabelName, LinkType, Stage, Status } from "allure-js-commons";
import { extractMetadataFromString, getMessageAndTraceFromError, getMetadataLabel, hasLabel, stripAnsi } from "allure-js-commons/sdk";
import { ALLURE_RUNTIME_MESSAGE_CONTENT_TYPE, ReporterRuntime, ShallowStepsStack, createDefaultWriter, createStepResult, escapeRegExp, formatLink, getEnvironmentLabels, getFrameworkLabel, getHostLabel, getLanguageLabel, getPackageLabel, getThreadLabel, getWorstTestStepResult, md5, parseTestPlan, randomUuid, readImageAsBase64 } from "allure-js-commons/sdk/reporter";
import { allurePlaywrightLegacyApi } from "./legacy.js";
import { AFTER_HOOKS_ROOT_STEP_TITLE, BEFORE_HOOKS_ROOT_STEP_TITLE, diffEndRegexp, isAfterHookStep, isBeforeHookStep, isDescendantOfStepWithTitle, normalizeAttachStepTitle, normalizeHookTitle, statusToAllureStats } from "./utils.js";
var _AllureReporter_brand = /*#__PURE__*/new WeakSet();
export var AllureReporter = /*#__PURE__*/function () {
  function AllureReporter(config) {
    _classCallCheck(this, AllureReporter);
    _classPrivateMethodInitSpec(this, _AllureReporter_brand);
    _defineProperty(this, "config", void 0);
    _defineProperty(this, "suite", void 0);
    _defineProperty(this, "options", void 0);
    _defineProperty(this, "outputDir", void 0);
    _defineProperty(this, "snapshotDir", void 0);
    _defineProperty(this, "allureRuntime", void 0);
    _defineProperty(this, "globalStartTime", new Date());
    _defineProperty(this, "processedDiffs", []);
    _defineProperty(this, "startedTestCasesTitlesCache", []);
    _defineProperty(this, "allureResultsUuids", new Map());
    _defineProperty(this, "attachmentTargets", new Map());
    _defineProperty(this, "beforeHooksStepsStack", new Map());
    _defineProperty(this, "afterHooksStepsStack", new Map());
    _defineProperty(this, "beforeHooksAttachmentsStack", new Map());
    _defineProperty(this, "afterHooksAttachmentsStack", new Map());
    _defineProperty(this, "pwStepUuid", new WeakMap());
    this.options = _objectSpread({
      suiteTitle: true,
      detail: true
    }, config);
  }
  return _createClass(AllureReporter, [{
    key: "onConfigure",
    value: function onConfigure(config) {
      this.config = config;
      this.outputDir = config.projects[0].outputDir;
      this.snapshotDir = config.projects[0].snapshotDir;
      var testPlan = parseTestPlan();
      if (!testPlan) {
        return;
      }

      // @ts-ignore
      var configElement = config[Object.getOwnPropertySymbols(config)[0]];
      if (!configElement) {
        return;
      }
      var testsWithSelectors = testPlan.tests.filter(function (test) {
        return test.selector;
      });
      var v1ReporterTests = [];
      var v2ReporterTests = [];
      var cliArgs = [];
      testsWithSelectors.forEach(function (test) {
        // eslint-disable-next-line @typescript-eslint/no-unnecessary-type-assertion
        if (!/#/.test(test.selector)) {
          v2ReporterTests.push(test);
          return;
        }
        v1ReporterTests.push(test);
      });

      // The path needs to be specific to the current OS. Otherwise, it may not match against the test file.
      var selectorToGrepPattern = function selectorToGrepPattern(selector) {
        return escapeRegExp(path.normalize("/".concat(selector)));
      };
      if (v2ReporterTests.length) {
        // we need to cut off column because playwright works only with line number
        var v2SelectorsArgs = v2ReporterTests
        // eslint-disable-next-line @typescript-eslint/no-unnecessary-type-assertion
        .map(function (test) {
          return test.selector.replace(/:\d+$/, "");
        }).map(selectorToGrepPattern);
        cliArgs.push.apply(cliArgs, _toConsumableArray(v2SelectorsArgs));
      }
      if (v1ReporterTests.length) {
        var v1SelectorsArgs = v1ReporterTests
        // we can filter tests only by absolute path, so we need to cut off test name
        // eslint-disable-next-line @typescript-eslint/no-unnecessary-type-assertion
        .map(function (test) {
          return test.selector.split("#")[0];
        }).map(selectorToGrepPattern);
        cliArgs.push.apply(cliArgs, _toConsumableArray(v1SelectorsArgs));
      }
      if (!cliArgs.length) {
        return;
      }
      configElement.cliArgs = cliArgs;
    }
  }, {
    key: "onError",
    value: function onError() {}
  }, {
    key: "onExit",
    value: function onExit() {}
  }, {
    key: "onStdErr",
    value: function onStdErr() {}
  }, {
    key: "onStdOut",
    value: function onStdOut() {}
  }, {
    key: "onBegin",
    value: function onBegin(suite) {
      this.suite = suite;
      this.allureRuntime = new ReporterRuntime(_objectSpread(_objectSpread({}, this.options), {}, {
        writer: createDefaultWriter({
          resultsDir: this.options.resultsDir
        })
      }));
    }
  }, {
    key: "onTestBegin",
    value: function onTestBegin(test) {
      var suite = test.parent;
      var titleMetadata = extractMetadataFromString(test.title);
      var project = suite.project();
      var testFilePath = path.relative(project === null || project === void 0 ? void 0 : project.testDir, test.location.file);
      var relativeFileParts = testFilePath.split(path.sep);
      var relativeFile = relativeFileParts.join("/");
      // root > project > file path > test.describe...
      var _suite$titlePath = suite.titlePath(),
        _suite$titlePath2 = _toArray(_suite$titlePath),
        suiteTitles = _suite$titlePath2.slice(3);
      var nameSuites = suiteTitles.length > 0 ? "".concat(suiteTitles.join(" "), " ") : "";
      var testCaseIdBase = "".concat(relativeFile, "#").concat(nameSuites).concat(test.title);
      var result = {
        name: titleMetadata.cleanTitle,
        labels: [].concat(_toConsumableArray(titleMetadata.labels), _toConsumableArray(getEnvironmentLabels())),
        links: _toConsumableArray(titleMetadata.links),
        parameters: [],
        steps: [],
        testCaseId: md5(testCaseIdBase),
        fullName: "".concat(relativeFile, ":").concat(test.location.line, ":").concat(test.location.column),
        titlePath: relativeFileParts.concat.apply(relativeFileParts, _toConsumableArray(suiteTitles))
      };
      result.labels.push(getLanguageLabel());
      result.labels.push(getFrameworkLabel("playwright"));
      result.labels.push(getPackageLabel(testFilePath));
      result.labels.push({
        name: "titlePath",
        value: suite.titlePath().join(" > ")
      });

      // support for earlier playwright versions
      if ("tags" in test) {
        var _ref;
        var tags = test.tags.map(function (tag) {
          return {
            name: LabelName.TAG,
            value: tag.startsWith("@") ? tag.substring(1) : tag
          };
        });
        (_ref = result.labels).push.apply(_ref, _toConsumableArray(tags));
      }
      if ("annotations" in test) {
        var _iterator = _createForOfIteratorHelper(test.annotations),
          _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var annotation = _step.value;
            if (annotation.type === "skip" || annotation.type === "fixme") {
              continue;
            }
            if (annotation.type === "issue") {
              var _this$options$links;
              result.links.push(formatLink((_this$options$links = this.options.links) !== null && _this$options$links !== void 0 ? _this$options$links : {}, {
                type: LinkType.ISSUE,
                url: annotation.description
              }));
              continue;
            }
            if (annotation.type === "tms" || annotation.type === "test_key") {
              var _this$options$links2;
              result.links.push(formatLink((_this$options$links2 = this.options.links) !== null && _this$options$links2 !== void 0 ? _this$options$links2 : {}, {
                type: LinkType.TMS,
                url: annotation.description
              }));
              continue;
            }
            if (annotation.type === "description") {
              result.description = annotation.description;
              continue;
            }
            var annotationLabel = getMetadataLabel(annotation.type, annotation.description);
            if (annotationLabel) {
              result.labels.push(annotationLabel);
              continue;
            }
            result.steps.push({
              name: "".concat(annotation.type, ": ").concat(annotation.description),
              status: Status.PASSED,
              stage: Stage.FINISHED,
              parameters: [],
              steps: [],
              attachments: [],
              statusDetails: {}
            });
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      }
      if (project !== null && project !== void 0 && project.name) {
        result.parameters.push({
          name: "Project",
          value: project.name
        });
      }
      if ((project === null || project === void 0 ? void 0 : project.repeatEach) > 1) {
        result.parameters.push({
          name: "Repetition",
          value: "".concat(test.repeatEachIndex + 1)
        });
      }
      var testUuid = this.allureRuntime.startTest(result);
      this.allureResultsUuids.set(test.id, testUuid);
      this.startedTestCasesTitlesCache.push(titleMetadata.cleanTitle);
    }
  }, {
    key: "onStepBegin",
    value: function onStepBegin(test, _result, step) {
      var _this$pwStepUuid$get2;
      var isRootBeforeHook = step.title === BEFORE_HOOKS_ROOT_STEP_TITLE;
      var isRootAfterHook = step.title === AFTER_HOOKS_ROOT_STEP_TITLE;
      var isRootHook = isRootBeforeHook || isRootAfterHook;
      var isBeforeHookDescendant = isBeforeHookStep(step);
      var isAfterHookDescendant = isAfterHookStep(step);
      var isHookStep = isBeforeHookDescendant || isAfterHookDescendant;
      var testUuid = this.allureResultsUuids.get(test.id);
      if (_assertClassBrand(_AllureReporter_brand, this, _shouldIgnoreStep).call(this, step)) {
        return;
      }
      var baseStep = _objectSpread(_objectSpread({}, createStepResult()), {}, {
        name: step.title,
        start: step.startTime.getTime(),
        stage: Stage.RUNNING,
        uuid: randomUuid()
      });
      if (["test.attach", "attach"].includes(step.category) && !isHookStep) {
        var _this$pwStepUuid$get, _this$attachmentTarge;
        var parent = step.parent ? (_this$pwStepUuid$get = this.pwStepUuid.get(step.parent)) !== null && _this$pwStepUuid$get !== void 0 ? _this$pwStepUuid$get : null : null;
        var targets = (_this$attachmentTarge = this.attachmentTargets.get(test.id)) !== null && _this$attachmentTarge !== void 0 ? _this$attachmentTarge : [];
        targets.push({
          name: normalizeAttachStepTitle(step.title),
          stepUuid: parent !== null && parent !== void 0 ? parent : undefined
        });
        this.attachmentTargets.set(test.id, targets);
        return;
      }
      if (isHookStep) {
        // Lazily initialize the hook stack if it doesn't exist (e.g., when detail: false and root hook was ignored)
        var stack = isBeforeHookDescendant ? this.beforeHooksStepsStack.get(test.id) : this.afterHooksStepsStack.get(test.id);
        if (!stack) {
          stack = new ShallowStepsStack();
          var rootHookStep = _objectSpread(_objectSpread({}, createStepResult()), {}, {
            name: isBeforeHookDescendant ? BEFORE_HOOKS_ROOT_STEP_TITLE : AFTER_HOOKS_ROOT_STEP_TITLE,
            start: step.startTime.getTime(),
            stage: Stage.RUNNING,
            uuid: randomUuid()
          });
          stack.startStep(rootHookStep);
          if (isBeforeHookDescendant) {
            this.beforeHooksStepsStack.set(test.id, stack);
          } else {
            this.afterHooksStepsStack.set(test.id, stack);
          }
        }
        if (["test.attach", "attach"].includes(step.category)) {
          var _this$attachmentTarge2;
          var hookStepWithUuid;
          stack.startStep(baseStep);
          var attachStack = isBeforeHookDescendant ? this.beforeHooksAttachmentsStack : this.afterHooksAttachmentsStack;
          stack.updateStep(function (stepResult) {
            var _attachStack$get;
            hookStepWithUuid = _objectSpread(_objectSpread({}, step), {}, {
              uuid: stepResult.uuid
            });
            stepResult.name = normalizeHookTitle(stepResult.name);
            stepResult.stage = Stage.FINISHED;
            attachStack.set(test.id, [].concat(_toConsumableArray((_attachStack$get = attachStack.get(test.id)) !== null && _attachStack$get !== void 0 ? _attachStack$get : []), [hookStepWithUuid]));
          });
          stack.stopStep();
          var _targets = (_this$attachmentTarge2 = this.attachmentTargets.get(test.id)) !== null && _this$attachmentTarge2 !== void 0 ? _this$attachmentTarge2 : [];
          _targets.push({
            name: normalizeAttachStepTitle(step.title),
            hookStep: hookStepWithUuid
          });
          this.attachmentTargets.set(test.id, _targets);
          return;
        }
        stack.startStep(baseStep);
        return;
      }
      if (isRootHook) {
        var _stack = new ShallowStepsStack();
        _stack.startStep(baseStep);
        if (isRootBeforeHook) {
          this.beforeHooksStepsStack.set(test.id, _stack);
        } else {
          this.afterHooksStepsStack.set(test.id, _stack);
        }
        return;
      }
      var parentUuid = step.parent ? (_this$pwStepUuid$get2 = this.pwStepUuid.get(step.parent)) !== null && _this$pwStepUuid$get2 !== void 0 ? _this$pwStepUuid$get2 : null : null;
      var createdUuid = this.allureRuntime.startStep(testUuid, parentUuid, baseStep);
      if (createdUuid) {
        this.pwStepUuid.set(step, createdUuid);
      }
    }
  }, {
    key: "onStepEnd",
    value: function onStepEnd(test, _result, step) {
      var isRootBeforeHook = step.title === BEFORE_HOOKS_ROOT_STEP_TITLE;
      var isRootAfterHook = step.title === AFTER_HOOKS_ROOT_STEP_TITLE;
      var isRootHook = isRootBeforeHook || isRootAfterHook;

      // For root hooks, check if we have a lazily created stack that needs to be finalized
      if (isRootHook) {
        var stack = isRootAfterHook ? this.afterHooksStepsStack.get(test.id) : this.beforeHooksStepsStack.get(test.id);

        // If stack exists (was lazily created), finalize the root hook step
        if (stack) {
          stack.updateStep(function (stepResult) {
            var _getWorstTestStepResu;
            var _ref2 = (_getWorstTestStepResu = getWorstTestStepResult(stepResult.steps)) !== null && _getWorstTestStepResu !== void 0 ? _getWorstTestStepResu : {},
              _ref2$status = _ref2.status,
              status = _ref2$status === void 0 ? Status.PASSED : _ref2$status;
            stepResult.status = step.error ? Status.FAILED : status;
            stepResult.stage = Stage.FINISHED;
            if (step.error) {
              stepResult.statusDetails = _objectSpread({}, getMessageAndTraceFromError(step.error));
            }
          });
          stack.stopStep({
            duration: step.duration
          });
        }
        return;
      }
      if (_assertClassBrand(_AllureReporter_brand, this, _shouldIgnoreStep).call(this, step)) {
        return;
      }
      // ignore test.attach steps since attachments are already in the report
      if (["test.attach", "attach"].includes(step.category)) {
        return;
      }
      var testUuid = this.allureResultsUuids.get(test.id);
      var isBeforeHookDescendant = isBeforeHookStep(step);
      var isAfterHookDescendant = isAfterHookStep(step);
      var isAfterHook = isAfterHookDescendant;
      var isHook = isBeforeHookDescendant || isAfterHookDescendant;
      if (isHook) {
        var _stack2 = isAfterHook ? this.afterHooksStepsStack.get(test.id) : this.beforeHooksStepsStack.get(test.id);

        // Stack might not exist if root hook was ignored (e.g., detail: false) and no test.step was called inside it
        if (!_stack2) {
          return;
        }
        _stack2.updateStep(function (stepResult) {
          var _getWorstTestStepResu2;
          var _ref3 = (_getWorstTestStepResu2 = getWorstTestStepResult(stepResult.steps)) !== null && _getWorstTestStepResu2 !== void 0 ? _getWorstTestStepResu2 : {},
            _ref3$status = _ref3.status,
            status = _ref3$status === void 0 ? Status.PASSED : _ref3$status;
          stepResult.status = step.error ? Status.FAILED : status;
          stepResult.stage = Stage.FINISHED;
          if (step.error) {
            stepResult.statusDetails = _objectSpread({}, getMessageAndTraceFromError(step.error));
          }
        });
        _stack2.stopStep({
          duration: step.duration
        });
        return;
      }
      var currentStep = this.allureRuntime.currentStep(testUuid);
      if (!currentStep) {
        return;
      }
      var stepUuid = this.pwStepUuid.get(step);
      if (!stepUuid) {
        return;
      }
      this.allureRuntime.updateStep(stepUuid, function (stepResult) {
        var _getWorstTestStepResu3;
        var _ref4 = (_getWorstTestStepResu3 = getWorstTestStepResult(stepResult.steps)) !== null && _getWorstTestStepResu3 !== void 0 ? _getWorstTestStepResu3 : {},
          _ref4$status = _ref4.status,
          status = _ref4$status === void 0 ? Status.PASSED : _ref4$status;
        stepResult.status = step.error ? Status.FAILED : status;
        stepResult.stage = Stage.FINISHED;
        if (step.error) {
          stepResult.statusDetails = _objectSpread({}, getMessageAndTraceFromError(step.error));
        }
      });
      this.allureRuntime.stopStep(stepUuid, {
        duration: step.duration
      });
    }
  }, {
    key: "onTestEnd",
    value: function () {
      var _onTestEnd = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee(test, result) {
        var _this = this,
          _this$beforeHooksAtta,
          _this$attachmentTarge3;
        var testUuid, threadId, thread, error, _test$parent$titlePat, _test$parent$titlePat2, projectSuiteTitle, fileSuiteTitle, suiteTitles, beforeHooksStack, afterHooksStack, attachmentsInBeforeHooks, targets, targetsByName, _iterator2, _step2, _targetsByName$get, t, q, takeByName, _iterator3, _step3, attachment, isRuntimeMessage, stepInfo, _stepInfo$hookStep$uu, _stepInfo$hookStep, stepUuid, hookStep, isBeforeHook, targetStack, stepResult, fileName, _t;
        return _regenerator().w(function (_context) {
          while (1) switch (_context.n) {
            case 0:
              testUuid = this.allureResultsUuids.get(test.id); // We need to check parallelIndex first because pw introduced this field only in v1.30.0
              threadId = result.parallelIndex !== undefined ? result.parallelIndex : result.workerIndex;
              thread = "pid-".concat(process.pid, "-worker-").concat(threadId);
              error = result.error; // only apply default suites if not set by user
              _test$parent$titlePat = test.parent.titlePath(), _test$parent$titlePat2 = _toArray(_test$parent$titlePat), projectSuiteTitle = _test$parent$titlePat2[1], fileSuiteTitle = _test$parent$titlePat2[2], suiteTitles = _test$parent$titlePat2.slice(3);
              beforeHooksStack = this.beforeHooksStepsStack.get(test.id);
              afterHooksStack = this.afterHooksStepsStack.get(test.id);
              this.allureRuntime.updateTest(testUuid, function (testResult) {
                testResult.labels.push(getHostLabel());
                testResult.labels.push(getThreadLabel(thread));
                if (projectSuiteTitle && !hasLabel(testResult, LabelName.PARENT_SUITE)) {
                  testResult.labels.push({
                    name: LabelName.PARENT_SUITE,
                    value: projectSuiteTitle
                  });
                }
                if (_this.options.suiteTitle && fileSuiteTitle && !hasLabel(testResult, LabelName.SUITE)) {
                  testResult.labels.push({
                    name: LabelName.SUITE,
                    value: fileSuiteTitle
                  });
                }
                if (suiteTitles.length > 0 && !hasLabel(testResult, LabelName.SUB_SUITE)) {
                  testResult.labels.push({
                    name: LabelName.SUB_SUITE,
                    value: suiteTitles.join(" > ")
                  });
                }
                if (error) {
                  testResult.statusDetails = _objectSpread({}, getMessageAndTraceFromError(error));
                } else {
                  var _test$annotations;
                  var skipReason = (_test$annotations = test.annotations) === null || _test$annotations === void 0 || (_test$annotations = _test$annotations.find(function (annotation) {
                    return annotation.type === "skip" || annotation.type === "fixme";
                  })) === null || _test$annotations === void 0 ? void 0 : _test$annotations.description;
                  if (skipReason) {
                    testResult.statusDetails = _objectSpread(_objectSpread({}, testResult.statusDetails), {}, {
                      message: skipReason
                    });
                  }
                }
                testResult.status = statusToAllureStats(result.status, test.expectedStatus);
                testResult.stage = Stage.FINISHED;
              });
              if (result.stdout.length > 0) {
                this.allureRuntime.writeAttachment(testUuid, undefined, "stdout", Buffer.from(stripAnsi(result.stdout.join("")), "utf-8"), {
                  contentType: ContentType.TEXT
                });
              }
              if (result.stderr.length > 0) {
                this.allureRuntime.writeAttachment(testUuid, undefined, "stderr", Buffer.from(stripAnsi(result.stderr.join("")), "utf-8"), {
                  contentType: ContentType.TEXT
                });
              }
              attachmentsInBeforeHooks = (_this$beforeHooksAtta = this.beforeHooksAttachmentsStack.get(test.id)) !== null && _this$beforeHooksAtta !== void 0 ? _this$beforeHooksAtta : []; // FIFO
              targets = (_this$attachmentTarge3 = this.attachmentTargets.get(test.id)) !== null && _this$attachmentTarge3 !== void 0 ? _this$attachmentTarge3 : [];
              targetsByName = new Map();
              _iterator2 = _createForOfIteratorHelper(targets);
              try {
                for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
                  t = _step2.value;
                  q = (_targetsByName$get = targetsByName.get(t.name)) !== null && _targetsByName$get !== void 0 ? _targetsByName$get : [];
                  q.push(t);
                  targetsByName.set(t.name, q);
                }
              } catch (err) {
                _iterator2.e(err);
              } finally {
                _iterator2.f();
              }
              takeByName = function takeByName(name) {
                var target = targetsByName.get(name);
                if (!target || target.length === 0) {
                  return undefined;
                }
                return target.shift();
              };
              _iterator3 = _createForOfIteratorHelper(result.attachments);
              _context.p = 1;
              _iterator3.s();
            case 2:
              if ((_step3 = _iterator3.n()).done) {
                _context.n = 9;
                break;
              }
              attachment = _step3.value;
              isRuntimeMessage = attachment.contentType === ALLURE_RUNTIME_MESSAGE_CONTENT_TYPE;
              stepInfo = takeByName(attachment.name);
              if (!isRuntimeMessage) {
                _context.n = 4;
                break;
              }
              stepUuid = (_stepInfo$hookStep$uu = stepInfo === null || stepInfo === void 0 || (_stepInfo$hookStep = stepInfo.hookStep) === null || _stepInfo$hookStep === void 0 ? void 0 : _stepInfo$hookStep.uuid) !== null && _stepInfo$hookStep$uu !== void 0 ? _stepInfo$hookStep$uu : stepInfo === null || stepInfo === void 0 ? void 0 : stepInfo.stepUuid;
              _context.n = 3;
              return this.processAttachment(testUuid, stepUuid, attachment);
            case 3:
              return _context.a(3, 8);
            case 4:
              if (!(stepInfo !== null && stepInfo !== void 0 && stepInfo.hookStep)) {
                _context.n = 5;
                break;
              }
              hookStep = stepInfo.hookStep;
              isBeforeHook = attachmentsInBeforeHooks.includes(hookStep);
              targetStack = isBeforeHook ? beforeHooksStack : afterHooksStack;
              if (targetStack) {
                stepResult = targetStack.findStepByUuid(hookStep.uuid);
                if (stepResult) {
                  fileName = targetStack.addAttachment(attachment, this.allureRuntime.writer);
                  stepResult.attachments.push({
                    name: attachment.name,
                    type: attachment.contentType,
                    source: fileName
                  });
                }
              }
              return _context.a(3, 8);
            case 5:
              if (!(stepInfo !== null && stepInfo !== void 0 && stepInfo.stepUuid)) {
                _context.n = 7;
                break;
              }
              _context.n = 6;
              return this.processAttachment(testUuid, stepInfo.stepUuid, attachment);
            case 6:
              return _context.a(3, 8);
            case 7:
              _context.n = 8;
              return this.processAttachment(testUuid, undefined, attachment);
            case 8:
              _context.n = 2;
              break;
            case 9:
              _context.n = 11;
              break;
            case 10:
              _context.p = 10;
              _t = _context.v;
              _iterator3.e(_t);
            case 11:
              _context.p = 11;
              _iterator3.f();
              return _context.f(11);
            case 12:
              // FIXME: temp logic for labels override, we need it here to keep the reporter compatible with v2 API
              // in next iterations we need to implement the logic for every javascript integration
              this.allureRuntime.updateTest(testUuid, function (testResult) {
                var mappedLabels = testResult.labels.reduce(function (acc, label) {
                  if (!acc[label.name]) {
                    acc[label.name] = [];
                  }
                  acc[label.name].push(label);
                  return acc;
                }, {});
                var newLabels = Object.keys(mappedLabels).flatMap(function (labelName) {
                  var labelsGroup = mappedLabels[labelName];
                  if (labelName === LabelName.SUITE || labelName === LabelName.PARENT_SUITE || labelName === LabelName.SUB_SUITE) {
                    return labelsGroup.slice(-1);
                  }
                  return labelsGroup;
                });
                if (beforeHooksStack) {
                  var _testResult$steps;
                  (_testResult$steps = testResult.steps).unshift.apply(_testResult$steps, _toConsumableArray(beforeHooksStack.steps));
                  _this.beforeHooksStepsStack["delete"](test.id);
                }
                if (afterHooksStack) {
                  var _testResult$steps2;
                  (_testResult$steps2 = testResult.steps).push.apply(_testResult$steps2, _toConsumableArray(afterHooksStack.steps));
                  _this.afterHooksStepsStack["delete"](test.id);
                }
                testResult.labels = newLabels;
              });
              this.allureRuntime.stopTest(testUuid, {
                duration: result.duration
              });
              this.allureRuntime.writeTest(testUuid);
              this.attachmentTargets["delete"](test.id);
            case 13:
              return _context.a(2);
          }
        }, _callee, this, [[1, 10, 11, 12]]);
      }));
      function onTestEnd(_x, _x2) {
        return _onTestEnd.apply(this, arguments);
      }
      return onTestEnd;
    }()
  }, {
    key: "addSkippedResults",
    value: function () {
      var _addSkippedResults = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2() {
        var _this2 = this;
        var unprocessedCases, _iterator4, _step4, testCase, _t2;
        return _regenerator().w(function (_context2) {
          while (1) switch (_context2.n) {
            case 0:
              unprocessedCases = this.suite.allTests().filter(function (_ref5) {
                var title = _ref5.title;
                var titleMetadata = extractMetadataFromString(title);
                return !_this2.startedTestCasesTitlesCache.includes(titleMetadata.cleanTitle);
              });
              _iterator4 = _createForOfIteratorHelper(unprocessedCases);
              _context2.p = 1;
              _iterator4.s();
            case 2:
              if ((_step4 = _iterator4.n()).done) {
                _context2.n = 4;
                break;
              }
              testCase = _step4.value;
              this.onTestBegin(testCase);
              _context2.n = 3;
              return this.onTestEnd(testCase, {
                status: Status.SKIPPED,
                attachments: [],
                duration: 0,
                errors: [],
                parallelIndex: 0,
                workerIndex: 0,
                retry: 0,
                steps: [],
                stderr: [],
                stdout: [],
                startTime: this.globalStartTime,
                annotations: []
              });
            case 3:
              _context2.n = 2;
              break;
            case 4:
              _context2.n = 6;
              break;
            case 5:
              _context2.p = 5;
              _t2 = _context2.v;
              _iterator4.e(_t2);
            case 6:
              _context2.p = 6;
              _iterator4.f();
              return _context2.f(6);
            case 7:
              return _context2.a(2);
          }
        }, _callee2, this, [[1, 5, 6, 7]]);
      }));
      function addSkippedResults() {
        return _addSkippedResults.apply(this, arguments);
      }
      return addSkippedResults;
    }()
  }, {
    key: "onEnd",
    value: function () {
      var _onEnd = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee3() {
        return _regenerator().w(function (_context3) {
          while (1) switch (_context3.n) {
            case 0:
              _context3.n = 1;
              return this.addSkippedResults();
            case 1:
              this.allureRuntime.writeEnvironmentInfo();
              this.allureRuntime.writeCategoriesDefinitions();
            case 2:
              return _context3.a(2);
          }
        }, _callee3, this);
      }));
      function onEnd() {
        return _onEnd.apply(this, arguments);
      }
      return onEnd;
    }()
  }, {
    key: "printsToStdio",
    value: function printsToStdio() {
      return false;
    }
  }, {
    key: "processStepMetadataMessage",
    value: function processStepMetadataMessage(attachmentStepUuid, message) {
      var _message$data = message.data,
        name = _message$data.name,
        _message$data$paramet = _message$data.parameters,
        parameters = _message$data$paramet === void 0 ? [] : _message$data$paramet;
      this.allureRuntime.updateStep(attachmentStepUuid, function (step) {
        var _step$parameters;
        if (name) {
          step.name = name;
        }
        (_step$parameters = step.parameters).push.apply(_step$parameters, _toConsumableArray(parameters));
      });
    }
  }, {
    key: "processAttachment",
    value: function () {
      var _processAttachment = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee6(testUuid, attachmentStepUuid, attachment) {
        var _replace;
        var allureRuntimeMessage, message, parentUuid, contentType, pathWithoutEndFromSnapshotDir, pathWithoutEnd, fileExists, readImageFromDirs, actualBase64, expectedBase64, diffBase64, diffName;
        return _regenerator().w(function (_context6) {
          while (1) switch (_context6.n) {
            case 0:
              if (!(!attachment.body && !attachment.path)) {
                _context6.n = 1;
                break;
              }
              return _context6.a(2);
            case 1:
              allureRuntimeMessage = attachment.contentType === ALLURE_RUNTIME_MESSAGE_CONTENT_TYPE;
              if (!(allureRuntimeMessage && !attachment.body)) {
                _context6.n = 2;
                break;
              }
              return _context6.a(2);
            case 2:
              if (!allureRuntimeMessage) {
                _context6.n = 4;
                break;
              }
              message = JSON.parse(attachment.body.toString());
              if (!(message.type === "step_metadata")) {
                _context6.n = 3;
                break;
              }
              this.processStepMetadataMessage(attachmentStepUuid, message);
              return _context6.a(2);
            case 3:
              this.allureRuntime.applyRuntimeMessages(testUuid, [message]);
              return _context6.a(2);
            case 4:
              parentUuid = this.allureRuntime.startStep(testUuid, attachmentStepUuid, {
                name: attachment.name
              }); // only stop if step is created. Step may not be created only if test with specified uuid doesn't exists.
              // usually, missing test by uuid means we should completely skip result processing;
              // the later operations are safe and will only produce console warnings
              if (parentUuid) {
                this.allureRuntime.stopStep(parentUuid, undefined);
              }
              if (!attachment.body) {
                _context6.n = 5;
                break;
              }
              this.allureRuntime.writeAttachment(testUuid, parentUuid, attachment.name, attachment.body, {
                contentType: attachment.contentType
              });
              _context6.n = 7;
              break;
            case 5:
              if (existsSync(attachment.path)) {
                _context6.n = 6;
                break;
              }
              return _context6.a(2);
            case 6:
              contentType = attachment.name === "trace" && attachment.contentType === "application/zip" ? "application/vnd.allure.playwright-trace" : attachment.contentType;
              this.allureRuntime.writeAttachment(testUuid, parentUuid, attachment.name, attachment.path, {
                contentType: contentType
              });
            case 7:
              if (attachment.name.match(diffEndRegexp)) {
                _context6.n = 8;
                break;
              }
              return _context6.a(2);
            case 8:
              pathWithoutEndFromSnapshotDir = (_replace = attachment.path.replace(this.outputDir, this.snapshotDir)) === null || _replace === void 0 ? void 0 : _replace.replace(diffEndRegexp, "").replace(/\.png/, "");
              pathWithoutEnd = attachment.path.replace(diffEndRegexp, "").replace(/\.png/, "");
              if (!(this.processedDiffs.includes(pathWithoutEnd) || this.processedDiffs.includes(pathWithoutEndFromSnapshotDir))) {
                _context6.n = 9;
                break;
              }
              return _context6.a(2);
            case 9:
              fileExists = /*#__PURE__*/function () {
                var _ref6 = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee4(filePath) {
                  return _regenerator().w(function (_context4) {
                    while (1) switch (_context4.n) {
                      case 0:
                        _context4.n = 1;
                        return access(filePath).then(function () {
                          return true;
                        })["catch"](function () {
                          return false;
                        });
                      case 1:
                        return _context4.a(2, _context4.v);
                    }
                  }, _callee4);
                }));
                return function fileExists(_x6) {
                  return _ref6.apply(this, arguments);
                };
              }();
              readImageFromDirs = /*#__PURE__*/function () {
                var _ref7 = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee5(modifier) {
                  var defaultPath, snapshotPath;
                  return _regenerator().w(function (_context5) {
                    while (1) switch (_context5.n) {
                      case 0:
                        defaultPath = "".concat(pathWithoutEnd, "-").concat(modifier, ".png");
                        snapshotPath = "".concat(pathWithoutEndFromSnapshotDir, "-").concat(modifier, ".png");
                        _context5.n = 1;
                        return fileExists(defaultPath);
                      case 1:
                        if (!_context5.v) {
                          _context5.n = 3;
                          break;
                        }
                        _context5.n = 2;
                        return readImageAsBase64(defaultPath);
                      case 2:
                        return _context5.a(2, _context5.v);
                      case 3:
                        _context5.n = 4;
                        return fileExists(snapshotPath);
                      case 4:
                        if (!_context5.v) {
                          _context5.n = 6;
                          break;
                        }
                        _context5.n = 5;
                        return readImageAsBase64(snapshotPath);
                      case 5:
                        return _context5.a(2, _context5.v);
                      case 6:
                        return _context5.a(2, undefined);
                    }
                  }, _callee5);
                }));
                return function readImageFromDirs(_x7) {
                  return _ref7.apply(this, arguments);
                };
              }();
              _context6.n = 10;
              return readImageFromDirs("actual");
            case 10:
              actualBase64 = _context6.v;
              _context6.n = 11;
              return readImageFromDirs("expected");
            case 11:
              expectedBase64 = _context6.v;
              _context6.n = 12;
              return readImageFromDirs("diff");
            case 12:
              diffBase64 = _context6.v;
              diffName = attachment.name.replace(diffEndRegexp, "");
              this.allureRuntime.writeAttachment(testUuid, undefined, diffName, Buffer.from(JSON.stringify({
                expected: expectedBase64,
                actual: actualBase64,
                diff: diffBase64,
                name: diffName
              }), "utf-8"), {
                contentType: ContentType.IMAGEDIFF,
                fileExtension: ".imagediff"
              });
              this.processedDiffs.push(pathWithoutEnd);
            case 13:
              return _context6.a(2);
          }
        }, _callee6, this);
      }));
      function processAttachment(_x3, _x4, _x5) {
        return _processAttachment.apply(this, arguments);
      }
      return processAttachment;
    }()
  }, {
    key: "version",
    value: function version() {
      return "v2";
    }
  }]);
}();

/**
 * @deprecated for removal, import functions directly from "allure-js-commons".
 */
function _shouldIgnoreStep(step) {
  if (!this.options.detail && !["test.step", "attach", "test.attach"].includes(step.category)) {
    return true;
  }

  // ignore noisy route.continue()
  if (step.category === "pw:api" && step.title === "route.continue()") {
    return true;
  }

  // playwright doesn't report this step
  if (step.title === "Worker Cleanup" || isDescendantOfStepWithTitle(step, "Worker Cleanup")) {
    return true;
  }
  return false;
}
export var allure = allurePlaywrightLegacyApi;

/**
 * @deprecated for removal, import functions directly from "@playwright/test".
 */
export { test, expect } from "@playwright/test";
export default AllureReporter;
//# sourceMappingURL=index.js.map