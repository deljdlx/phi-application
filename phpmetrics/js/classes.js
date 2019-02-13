var classes = [
    {
        "name": "Phi\\Application\\Application",
        "interface": false,
        "abstract": false,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getFilepathRoot",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "initialize",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getExecutedRoutes",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getContainer",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getContainers",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "setContainer",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "addContainer",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "exists",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "set",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "get",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "setCallback",
                "role": "setter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "executeRoute",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getRouter",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "setRequest",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getRequest",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "run",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getValidatedRoutes",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "runRouters",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "addHeader",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getHeaders",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "isHTMLResponse",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getPath",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getReturnValue",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getOutput",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "setOutput",
                "role": "setter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "sendHeaders",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getResponses",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "setDatasources",
                "role": "setter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getDatasource",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "autobuild",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "setDefaultRouter",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getDefaultRouter",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getRouters",
                "role": "getter",
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "addRouter",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "addRoute",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            },
            {
                "name": "getDefaultContainer",
                "role": null,
                "public": false,
                "private": true,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 37,
        "nbMethods": 26,
        "nbMethodsPrivate": 2,
        "nbMethodsPublic": 24,
        "nbMethodsGetter": 8,
        "nbMethodsSetters": 3,
        "wmc": 84,
        "ccn": 48,
        "ccnMethodMax": 10,
        "externals": [
            "Phi\\Container\\Interfaces\\Container",
            "Exception",
            "Phi\\Container\\Interfaces\\Container",
            "Phi\\Container\\Interfaces\\Container",
            "Phi\\Core\\Exception",
            "Phi\\Routing\\Request",
            "Phi\\Routing\\Request",
            "Phi\\Routing\\Request",
            "Phi\\Routing\\Request",
            "Phi\\Routing\\Request",
            "Phi\\Routing\\Request",
            "Phi\\HTTP\\Header",
            "Phi\\Routing\\Router",
            "Phi\\Routing\\Router",
            "Phi\\Routing\\Router",
            "Phi\\Routing\\Route",
            "Planck\\Container"
        ],
        "parents": [],
        "lcom": 1,
        "length": 470,
        "vocabulary": 63,
        "volume": 2809.32,
        "difficulty": 31.04,
        "effort": 87194.98,
        "level": 0.03,
        "bugs": 0.94,
        "time": 4844,
        "intelligentContent": 90.51,
        "number_operators": 141,
        "number_operands": 329,
        "number_operators_unique": 10,
        "number_operands_unique": 53,
        "cloc": 86,
        "loc": 442,
        "lloc": 356,
        "mi": 45.31,
        "mIwoC": 13.74,
        "commentWeight": 31.57,
        "kanDefect": 5.08,
        "relativeStructuralComplexity": 784,
        "relativeDataComplexity": 1.55,
        "relativeSystemComplexity": 785.55,
        "totalStructuralComplexity": 29008,
        "totalDataComplexity": 57.52,
        "totalSystemComplexity": 29065.52,
        "package": "Phi\\",
        "pageRank": 0.66,
        "afferentCoupling": 1,
        "efferentCoupling": 8,
        "instability": 0.89,
        "violations": {}
    },
    {
        "name": "Phi\\Application\\Controller",
        "interface": false,
        "abstract": false,
        "methods": [
            {
                "name": "__construct",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 1,
        "nbMethods": 1,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 1,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "wmc": 2,
        "ccn": 2,
        "ccnMethodMax": 2,
        "externals": [
            "Phi\\Application\\Application"
        ],
        "parents": [],
        "lcom": 1,
        "length": 6,
        "vocabulary": 4,
        "volume": 12,
        "difficulty": 2,
        "effort": 24,
        "level": 0.5,
        "bugs": 0,
        "time": 1,
        "intelligentContent": 6,
        "number_operators": 2,
        "number_operands": 4,
        "number_operators_unique": 2,
        "number_operands_unique": 2,
        "cloc": 3,
        "loc": 14,
        "lloc": 11,
        "mi": 102.32,
        "mIwoC": 69.46,
        "commentWeight": 32.86,
        "kanDefect": 0.22,
        "relativeStructuralComplexity": 0,
        "relativeDataComplexity": 1,
        "relativeSystemComplexity": 1,
        "totalStructuralComplexity": 0,
        "totalDataComplexity": 1,
        "totalSystemComplexity": 1,
        "package": "Phi\\Application\\",
        "pageRank": 0.17,
        "afferentCoupling": 0,
        "efferentCoupling": 1,
        "instability": 1,
        "violations": {}
    },
    {
        "name": "Phi\\Application\\RouteConfiguration",
        "interface": false,
        "abstract": true,
        "methods": [
            {
                "name": "getRoutes",
                "role": null,
                "public": true,
                "private": false,
                "_type": "Hal\\Metric\\FunctionMetric"
            }
        ],
        "nbMethodsIncludingGettersSetters": 1,
        "nbMethods": 1,
        "nbMethodsPrivate": 0,
        "nbMethodsPublic": 1,
        "nbMethodsGetter": 0,
        "nbMethodsSetters": 0,
        "wmc": 1,
        "ccn": 1,
        "ccnMethodMax": 1,
        "externals": [],
        "parents": [],
        "lcom": 1,
        "length": 0,
        "vocabulary": 0,
        "volume": 0,
        "difficulty": 0,
        "effort": 0,
        "level": 0,
        "bugs": 0,
        "time": 0,
        "intelligentContent": 0,
        "number_operators": 0,
        "number_operands": 0,
        "number_operators_unique": 0,
        "number_operands_unique": 0,
        "cloc": 3,
        "loc": 8,
        "lloc": 5,
        "mi": 211.63,
        "mIwoC": 171,
        "commentWeight": 40.63,
        "kanDefect": 0.15,
        "relativeStructuralComplexity": 0,
        "relativeDataComplexity": 0,
        "relativeSystemComplexity": 0,
        "totalStructuralComplexity": 0,
        "totalDataComplexity": 0,
        "totalSystemComplexity": 0,
        "package": "Phi\\Application\\",
        "pageRank": 0.17,
        "afferentCoupling": 0,
        "efferentCoupling": 0,
        "instability": 0,
        "violations": {}
    }
]