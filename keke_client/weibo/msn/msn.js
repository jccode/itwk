(function() {
	if (!window.WL) {
		var Rc = "WL.Internal.jsonp.", ic = 2000, Cc = "body", x = "callback", gb = "code", yc = "error", af = "error_description", pc = "logging", df = "tracing", cb = "message", y = "method", Qd = "file_input", Xd = "file_name", Jd = "file_output", Rd = "overwrite", z = "path", re = "pretty", ff = "result", gf = "status", Ed = "return_ssl_resources", cf = "success", hf = "error", ac = "suppress_redirects", bd = "suppress_response_codes", ob = "x_http_live_library", zc = 0, bc = 1, e = "access_token", lb = "authentication_token", t = "client_id", Pb = "display", Ve = "code", h = "error", j = "error_description", C = "expires", K = "expires_in", Pe = "locale", q = "redirect_uri", I = "response_type", r = "request_ts", f = "scope", Jc = "session", Cb = "secure_cookie", Rb = "state", d = "status", fb = [
				e, lb, f, K, C ], l = "connected", Ac = "notConnected", u = "unchecked", D = "unknown", mb = "expiring", qf = "expired", lf = "live-sdk-upload", ef = "live-sdk-download", Te = "appId", Ge = "channelUrl", Ic = "wl_auth", pf = "app", Fc = "popup", of = "page", Hb = "touch", Lb = "none", db = "auth.login", Ab = "auth.logout", T = "auth.sessionChange", qb = "auth.statusChange", Mc = "wl.log", wb = "access_denied", rd = "connection_failed", be = "invalid_cookie", wc = "request_canceled", Y = "request_failed", Dc = "timed_out", ke = "unknown_user", Sd = "user_rejected", jd = "The request could not be completed due to browser issues.", kd = "The request could not be completed due to browser limitations.", je = "METHOD: The operation has been canceled.", Wb = "The 'wl_auth' cookie is not valid.", Vb = "The 'wl_auth' cookie has been modified incorrectly. Ensure that the redirect URI only modifies sub-keys for values received from the OAuth endpoint.", Vc = "The 'wl_auth' cookie has multiple values. Ensure that the redirect URI specifies a cookie domain and path when setting cookies.", xd = "METHOD: The input parameter 'PARAM' does not reference a valid DOM element.", Kd = "METHOD: An exception was received for EVENT. Detail: MESSAGE", yd = "METHOD: The WL object must be initialized with WL.init() prior to invoking this method.", Zb = "A connection to the server could not be established.", Ye = "The user could not be identified.", sd = "The pending login request has been canceled.", ld = "METHOD: The input parameter 'PARAM' is not valid.", md = "METHOD: The input parameter 'PARAM' must be included.", Zc = "METHOD: The type of the provided value for the input parameter 'PARAM' is not valid.", Tc = "WL.login: There is a pending WL.login request, the current call will be ignored.", Yc = "METHOD: The input parameter 'redirect_uri' is required if the value of the 'response_type' parameter is 'code'.", Tb = "METHOD: The api call is not supported on this platform.", Xe = "WL.init: The response_type value 'code' is not supported on this platform.", ce = "METHOD: The input parameter 'redirect_uri' must use https: to match the scheme of the current page.", nd = "The auth request is timed out.", zd = "The popup is closed without receiving consent.", Bb = 0, ed = 1, Xb = 2, de = 3, eb = "get", ue = "post", mf = "put", fe = "delete", jf = "copy", kf = "move", ud = 30000, p = "onSuccess", w = "onError", E = "onProgress", ge = "code", ab = "token", Mb = "https:", Ob = "http:", Ke = "wl.signin", oe = /\s|,/, R = "boolean", k = "function", Oe = "number", c = "string", B = "object", zb = "string_or_array", He = "name", Eb = "element", ib = "brand", Kb = "type", X = "sign_in_text", U = "sign_out_text", jb = "theme", he = "messenger", ve = "hotmail", pe = "skydrive", Db = "windows", fc = "windowslive", Jb = "none", Qb = "signin", sb = Qb, hc = "login", cc = "connect", dc = "custom", gc = "blue", Id = "white", bf = "dark", Ze = "light", S = "sdk_root", Sb = "wl_trace", v = {
			name : x,
			type : k,
			optional : true
		}, dd = {
			name : x,
			type : k,
			optional : false
		};
		window.WL = {
			init : function(e) {
				try {
					var b = A(e);
					kb(b, {
						name : "properties",
						type : "properties",
						optional : false,
						properties : [ {
							name : t,
							altName : Te,
							type : c,
							optional : !Ae()
						}, {
							name : f,
							type : zb,
							optional : true
						}, {
							name : q,
							altName : Ge,
							type : "url",
							optional : true
						}, {
							name : I,
							type : c,
							allowedValues : [ ge, ab ],
							optional : true
						}, {
							name : pc,
							type : R,
							optional : true
						}, {
							name : d,
							type : R,
							optional : true
						} ]
					}, "WL.init");
					if (!b[q] && b[I] === Ve)
						throw new Error(Yc.replace("METHOD", "WL.init"));
					if (b[d] == null)
						b[d] = true;
					a.appInit(b)
				} catch (g) {
					m(g.message)
				}
			},
			login : function() {
				try {
					var b = P(arguments);
					o(b, [ {
						name : f,
						type : zb,
						optional : true
					}, {
						name : q,
						type : "url",
						optional : true
					}, v ], "WL.login");
					return a.login(b)
				} catch (e) {
					return F("WL.login", e)
				}
			},
			getSession : function() {
				try {
					return a.getSession()
				} catch (b) {
					m(b.message)
				}
			},
			getLoginStatus : function() {
				try {
					return a.getLoginStatus({
						callback : mc(arguments, k, 2),
						internal : false
					}, mc(arguments, R, 2))
				} catch (d) {
					return F("WL.getLoginStatus", d)
				}
			},
			logout : function(b) {
				try {
					kb(b, v, "WL.logout");
					return a.logout({
						callback : b
					})
				} catch (c) {
					return F("WL.logout", c)
				}
			},
			ui : function() {
				try {
					var b = P(arguments);
					o(b, [ {
						name : He,
						type : c,
						allowedValues : [ Qb ],
						optional : false
					}, v ], "WL.ui");
					a.ui(b)
				} catch (f) {
					m(f.message)
				}
			},
			api : function() {
				try {
					var b = Gd(arguments);
					o(b, [ {
						name : z,
						type : c,
						optional : false
					}, {
						name : y,
						type : c,
						optional : true
					}, v ], "WL.api");
					return a.api(b)
				} catch (f) {
					return F("WL.api", f)
				}
			},
			download : function() {
				try {
					if (!a.download)
						throw new Error(Tb.replace("METHOD", "WL.download"));
					var b = P(arguments);
					o(b, [ {
						name : z,
						type : c,
						optional : false
					}, {
						name : Jd,
						type : B,
						optional : false
					}, v ], "WL.download");
					return a.download(b)
				} catch (f) {
					return F("WL.download", f)
				}
			},
			upload : function() {
				try {
					if (!a.upload)
						throw new Error(Tb.replace("METHOD", "WL.upload"));
					var b = P(arguments);
					o(b, [ {
						name : z,
						type : c,
						optional : false
					}, {
						name : Xd,
						type : c,
						optional : true
					}, {
						name : Qd,
						type : B,
						optional : false
					}, {
						name : Rd,
						type : R,
						optional : true
					}, v ], "WL.upload");
					return a.upload(b)
				} catch (f) {
					return F("WL.upload", f)
				}
			}
		};
		var Ec = [ db, Ab, T, qb, Mc ];
		WL.Event = {
			subscribe : function(d, a) {
				try {
					kb([ d, a ], [ {
						name : "event",
						type : c,
						allowedValues : Ec,
						caseSensitive : true,
						optional : false
					}, dd ], "WL.Event.subscribe");
					b.subscribe(d, a)
				} catch (e) {
					m(e.message)
				}
			},
			unsubscribe : function(d, a) {
				try {
					kb([ d, a ], [ {
						name : "event",
						type : c,
						allowedValues : Ec,
						caseSensitive : true,
						optional : false
					}, v ], "WL.Event.unsubscribe");
					b.unsubscribe(d, a)
				} catch (e) {
					m(e.message)
				}
			}
		};
		WL.Internal = {};
		function P(g) {
			var e = Lc(g), a = null, b = null;
			for ( var d = 0; d < e.length; d++) {
				var c = e[d], f = typeof c;
				if (f === B && a === null)
					a = A(c);
				else if (f === k && b === null)
					b = c
			}
			a = a || {};
			if (b)
				a.callback = b;
			return a
		}
		function Gd(e) {
			var a = Lc(e), d = null, b = null;
			if (typeof a[0] === c) {
				d = a.shift();
				if (typeof a[0] === c)
					b = a.shift()
			}
			normalizedArgs = P(a);
			if (d !== null) {
				normalizedArgs[z] = d;
				if (b != null)
					normalizedArgs[y] = b
			}
			return normalizedArgs
		}
		function F(a, c) {
			var b = Yb(a, a, c);
			m(b.message);
			return V(a, false, null, b)
		}
		var b = {
			subscribe : function(a, c) {
				g("Subscribe " + a);
				var d = b.getHandlers(a);
				d.push(c)
			},
			unsubscribe : function(d, f) {
				g("Unsubscribe " + d);
				var c = b.getHandlers(d), e = [];
				if (f != null) {
					var h = false;
					for ( var a = 0; a < c.length; a++)
						if (h || c[a] != f)
							e.push(c[a]);
						else
							h = true
				}
				b._eHandlers[d] = e
			},
			getHandlers : function(c) {
				if (!b._eHandlers)
					b._eHandlers = {};
				var a = b._eHandlers[c];
				if (a == null)
					b._eHandlers[c] = a = [];
				return a
			},
			notify : function(d, e) {
				g("Notify " + d);
				var c = b.getHandlers(d);
				for ( var a = 0; a < c.length; a++)
					c[a](e)
			}
		}, a = {
			_status : zc,
			_statusRequests : []
		};
		a.appInit = function(c) {
			if (a._status == bc)
				return;
			var e = WL[S];
			if (e) {
				if (e.charAt(e.length - 1) !== "/")
					e += "/";
				a[S] = e
			}
			var i = c[pc];
			if (i === false)
				a._logEnabled = i;
			a._authScope = kc(c[f]);
			a._secureCookie = Hd(c[Cb]);
			od(c);
			var h = new L(c[t], Ic);
			a._session = h;
			a._status = bc;
			var g = h.getNormalStatus(), j = g[d];
			if (j == l) {
				b.notify(T, g);
				b.notify(qb, g);
				b.notify(db, g)
			} else if (c[d])
				a.getLoginStatus({
					internal : true
				}, Wc())
		};
		a.onloadInit = function() {
			De();
			Ee()
		};
		function J(b) {
			if (a._status === zc)
				throw new Error(yd.replace("METHOD", b))
		}
		a.api = function(a) {
			J("WL.api");
			var c = a[Cc];
			if (c) {
				a = A(Fb(c), a);
				delete a[Cc]
			}
			var b = a[y];
			a[y] = (b != null ? N(b) : eb).toLowerCase();
			return (new Kc(a)).execute()
		};
		var Ld = function() {
			var b = a.api.lastId, c;
			b = b === undefined ? 1 : b + 1;
			c = "WLAPI_REQ_" + b + "_" + (new Date).getTime();
			a.api.lastId = b;
			return c
		}, Kc = function(b) {
			var c = this;
			c._properties = b;
			c._completed = false;
			c._id = Ld();
			b[re] = false;
			b[Ed] = a._isHttps;
			b[ob] = a[ob];
			var d = b[z];
			c._url = se() + (d.charAt(0) === "/" ? d.substring(1) : d);
			c._promise = new n("WL.api", null, null)
		};
		Kc.prototype = {
			execute : function() {
				le(this);
				return this._promise
			},
			onCompleted : function(a) {
				if (this._completed)
					return;
				this._completed = true;
				Q(this._properties.callback, a, true);
				if (a[h])
					this._promise[w](a);
				else
					this._promise[p](a)
			}
		};
		function yb(e, c, a, d) {
			a = a ? N(a) : "";
			var b = a !== "" ? xe(a) : null;
			if (b === null) {
				b = {};
				if (c / 100 !== 2)
					b[yc] = ie(c, d)
			}
			e.onCompleted(b)
		}
		function ie(c, b) {
			var a = {};
			a[gb] = Y;
			a[cb] = b || Zb;
			return a
		}
		function ec() {
			var c = null, b = a._session.getStatus();
			if (b.status === mb || b.status === l)
				c = b.session[e];
			return c
		}
		function Fb(i) {
			var c = {};
			for ( var b in i) {
				var a = i[b], j = typeof a;
				if (a instanceof Array)
					for ( var d = 0; d < a.length; d++) {
						var f = a[d], l = typeof f;
						if (j == B && !(a instanceof Date)) {
							var h = Fb(f);
							for ( var e in h)
								c[b + "." + d + "." + e] = h[e]
						} else
							c[b + "." + d] = f
					}
				else if (j == B && !(a instanceof Date)) {
					var k = Fb(a);
					for ( var g in k)
						c[b + "." + g] = k[g]
				} else
					c[b] = a
			}
			return c
		}
		function Od(c) {
			if (!Se())
				return false;
			var b = tc(c), a = new XMLHttpRequest;
			a.open(b.method, b.url, true);
			var d = c._properties[y];
			if (b.method != eb)
				a.setRequestHeader("Content-Type",
						"application/x-www-form-urlencoded");
			a.onreadystatechange = function() {
				if (a.readyState == 4)
					yb(c, a.status, a.responseText)
			};
			a.send(b.body);
			return true
		}
		function tc(d) {
			var a = qc(d._properties, null, [ x, z, y ]), f = d._properties[y], g = jc(
					d._url, {
						"ts" : (new Date).getTime()
					}), h = ec(), c, b;
			a[ac] = "true";
			a[bd] = "true";
			if (h != null)
				a[e] = h;
			if (f === eb || f === fe) {
				c = null;
				b = eb;
				g += "&" + H(a)
			} else {
				c = H(a);
				b = ue
			}
			g += "&method=" + f;
			return {
				url : g,
				method : b,
				body : c
			}
		}
		a.login = function(b, d) {
			J("WL.login");
			Td(b);
			if (!ee(d))
				return V("WL.login", false, null, ub(Y, Tc));
			var c = a._session.tryGetResponse(b.normalizedScope);
			if (c != null)
				return V("WL.login", true, b.callback, c);
			a._pendingLogin = ae(b, Ad);
			return a._pendingLogin.execute()
		};
		function Ad(d, b) {
			a._pendingLogin = null;
			var c = b[h];
			if (c) {
				if (c == Sd)
					g("wl_app-onAuthRequestCompleted: " + b[j]);
				else
					nb("WL.login: " + b[j]);
				return
			} else
				Q(d.callback, b, true)
		}
		function Td(c) {
			var b = kc(c[f]);
			if (b === "")
				b = a._authScope;
			if (!b || b === "")
				throw pb(f, "WL.login");
			c.normalizedScope = b
		}
		function kc(b) {
			var a = b || "";
			if (a instanceof Array)
				a = a.join(" ");
			return N(a)
		}
		a.getSession = function() {
			J("WL.getSession");
			return a._session.getStatus()[Jc]
		};
		a.getLoginStatus = function(b, f) {
			J("WL.getLoginStatus");
			b = b || {};
			if (!f) {
				var d = a._session.tryGetResponse();
				if (d)
					return V("WL.getLoginStatus", true, b.callback, d)
			}
			g("wl_app:getLoginStatus");
			var e = a._statusRequests, c = null;
			if (!a._pendingStatusRequest) {
				c = id(b, gd);
				a._pendingStatusRequest = c
			}
			e.push(b);
			if (c != null)
				c.execute();
			return a._pendingStatusRequest._promise
		};
		function gd(k, b) {
			var f = a._statusRequests;
			a._pendingStatusRequest = null;
			g("wl_app:onGetLoginStatusCompleted");
			var d = b[h], e = false;
			while (f.length > 0) {
				var c = f.shift(), i = A(b);
				if (!d || c.internal)
					Q(c.callback, i, true);
				if (!c.internal)
					e = true
			}
			if (d)
				if (e && d !== Dc)
					nb("WL.getLoginStatus: " + b[j]);
				else
					g("wl_app-onGetLoginStatusCompleted: " + b[j])
		}
		a.logout = function(e) {
			J("WL.logout");
			var c = new n("WL.logout", null, null), d = function() {
				var a = b.getNormalStatus();
				Q(e.callback, a, false);
				c[p](a)
			}, b = a._session;
			if (b.isSignedIn()) {
				b.updateStatus(D);
				me(d)
			} else
				d();
			return c
		};
		a.ui = function(a) {
			J("WL.ui");
			if (a.name === Qb)
				new Ib(a)
		};
		a.ui.styles = {};
		var Ib = function(c) {
			var a = this;
			a._properties = c;
			var b = i(a, a.init);
			bb(b)
		};
		Ib.prototype = {
			init : function() {
				if (this._inited === true)
					return;
				this._inited = true;
				try {
					this.validate();
					var e = this, d = e._properties, f = d[Eb], g = d[Kb], k = d[x], j = d[X], h = d[U];
					cd(d);
					f = typeof f === c ? hb(d[Eb]) : f;
					e._element = f;
					g = g != null ? g : sb;
					if (g == sb) {
						j = O.signIn;
						h = O.signOut
					} else if (g == hc) {
						j = O.login;
						h = O.logout
					} else if (g == cc) {
						j = O.connect;
						h = O.signOut
					}
					e[X] = j;
					e[U] = h;
					Le(f, vd(d));
					e.setText(a._session.isSignedIn() ? h : j);
					Xc(e, f.childNodes[0]);
					b.subscribe(db, i(e, e.onLoggedIn));
					b.subscribe(Ab, i(e, e.onLoggedOut));
					a.getLoginStatus({
						internal : true
					});
					delete d[x];
					Q(k, d, false)
				} catch (l) {
					m(l.message)
				}
			},
			validate : function() {
				var a = this._properties;
				o(a, [ {
					name : Eb,
					type : "dom",
					optional : false
				}, {
					name : Kb,
					allowedValues : [ sb, hc, cc, dc ],
					type : c,
					optional : true
				}, {
					name : f,
					type : zb,
					optional : true
				} ], "WL.ui(name:'signin')");
				Qc(a);
				var b = a[Kb];
				if (b == dc)
					o(a, [ {
						name : X,
						type : c,
						optional : false
					}, {
						name : U,
						type : c,
						optional : false
					} ], "WL.ui(name:'signin')")
			},
			onClick : function() {
				if (this._element.childNodes.length == 0) {
					Ub(this);
					return
				}
				if (a._session.isSignedIn())
					a.logout({});
				else
					a.login(this._properties, true);
				return false
			},
			onLoggedIn : function() {
				this.setText(this[U])
			},
			onLoggedOut : function() {
				this.setText(this[X])
			}
		};
		function cd(b) {
			if (a._authScope && a._authScope !== "")
				b[f] = a._authScope;
			if (!b[f])
				b[f] = Ke
		}
		function Je() {
			return a[S] + "images"
		}
		function Uc(d, a, c) {
			a._handlers = a._handlers || {};
			var b = i(a, c);
			a._handlers[d] = b;
			return b
		}
		function ad(b, a) {
			return a._handlers[b]
		}
		function Qe(c) {
			var a = document.cookie;
			c += "=";
			var b = a.indexOf(c);
			if (b >= 0) {
				b += c.length;
				var d = a.indexOf(";", b);
				if (d < 0)
					d = a.length;
				else {
					postCookie = a.substring(d);
					if (postCookie.indexOf(c) >= 0)
						throw new Error(Vc)
				}
				var e = a.substring(b, d);
				return e
			}
			return ""
		}
		function Nc(g, d, f, e) {
			d = d ? d : "";
			var c = g + "=" + d + "; path=/";
			if (f && f != "localhost")
				c += "; domain=" + encodeURIComponent(f);
			if (e != null) {
				var b = new Date(0);
				if (e > 0) {
					b = new Date;
					b.setTime(b.getTime() + e * 1000)
				}
				c += ";expires=" + b.toUTCString()
			}
			if (a._isHttps && a._secureCookie)
				c += ";secure";
			document.cookie = c
		}
		var M = function(a, b) {
			this._cookieName = a;
			this._states = b || {};
			this._listeners = []
		};
		M.prototype = {
			getStates : function() {
				return this._states
			},
			get : function(a) {
				return this._states[a]
			},
			set : function(b, a) {
				this._states[b] = a
			},
			remove : function(a) {
				if (this._states[a])
					delete this._states[a]
			},
			load : function() {
				try {
					var a = Qe(this._cookieName);
					if (this._rawValue != a) {
						g("Cookie changed: " + this._cookieName + "=" + a);
						this._rawValue = a;
						this._states = W(a);
						for ( var b = 0; b < this._listeners.length; b++)
							this._listeners[b]()
					}
				} catch (c) {
					m(c.message);
					this.stopMonitor()
				}
			},
			flush : function(a) {
				this._states = a;
				this.save()
			},
			populate : function(a) {
				A(a, this._states);
				this.save()
			},
			save : function() {
				Nc(this._cookieName, H(this._states), Bc(), null)
			},
			clear : function() {
				this._states = {};
				Nc(this._cookieName, "", Bc(), 0)
			}
		};
		var L = function(b, a) {
			this._state = {};
			this._state[t] = b;
			this._state[d] = u;
			this._cookieName = a;
			this.init()
		};
		L.prototype = {
			get : function(a) {
				return this._state[a]
			},
			save : function() {
				if (this._stateDirty) {
					this._cookie.flush(this._state);
					this._stateDirty = false
				}
			},
			init : function() {
				var a = new M(this._cookieName);
				a.load();
				this._cookie = a;
				if (a.get(t) != this._state[t]) {
					a.clear();
					a.flush(this._state)
				} else
					this._state = a.getStates();
				this.initPlatformSpecific()
			},
			refresh : function() {
				a.getLoginStatus({
					internal : true
				}, true);
				this._refresh = undefined
			},
			scheduleRefresh : function() {
				this.cancelRefresh();
				var a = (this.tokenExpiresIn() - 600) * 1000;
				if (a > 0)
					this._refresh = window.setTimeout(i(this, this.refresh), a)
			},
			cancelRefresh : function() {
				if (this._refresh) {
					window.clearTimeout(this._refresh);
					this._refresh = undefined
				}
			},
			updateStatus : function(a) {
				var c = this._state[d], f = this._state[e];
				if (c != a) {
					this._state[d] = a;
					this._stateDirty = true;
					this.onStatusChanged(c, a);
					this.save();
					if (f != this._state[e])
						b.notify(T, this.getNormalStatus())
				}
			},
			onStatusChanged : function(c, a) {
				g("AuthSession: Auth status changed: " + c + "=>" + a);
				if (c != a) {
					var h = c == l, d = a == l;
					if (!d) {
						for ( var e = 0; e < fb.length; e++) {
							var f = fb[e];
							if (this._state[f])
								delete this._state[f]
						}
						this._stateDirty = true;
						this.save()
					}
					if (rb(c) != rb(a))
						b.notify(qb, this.getNormalStatus());
					if (d != h)
						if (d)
							b.notify(db, this.getNormalStatus());
						else
							b.notify(Ab, this.getNormalStatus())
				}
			},
			isSignedIn : function() {
				return this._state[d] === l
			},
			getNormalStatus : function() {
				var a = this.getStatus();
				a[d] = rb(a[d]);
				return a
			},
			tokenExpiresIn : function() {
				var a = this._state, c = a[d], b = parseInt(a[C]);
				if (c === l)
					return b - sc();
				return -1
			}
		};
		function xc(f, a) {
			if (a == null || N(a) == "")
				return true;
			var c = a.split(oe), e = f.join(" ");
			for ( var b = 0; b < c.length; b++) {
				var d = N(c[b]);
				if (d != "" && e.indexOf(d) < 0)
					return false
			}
			return true
		}
		function N(a) {
			return a.replace(/^\s+|\s+$/g, "")
		}
		function A(b, d) {
			var c = d || {};
			if (b != null)
				for ( var a in b)
					c[a] = b[a];
			return c
		}
		function qc(e, d, b) {
			var c = A(e, d);
			for ( var a = 0; a < b.length; a++)
				delete c[b[a]];
			return c
		}
		function Lc(a) {
			return Array.prototype.slice.call(a)
		}
		function i(b, a) {
			return function() {
				if (typeof a === k)
					return a.apply(b, arguments)
			}
		}
		function Oc(a, e) {
			a = "[WL]" + a;
			var b = window.console;
			if (b && b.log)
				switch (e) {
				case "warning":
					b.warn(a);
					break;
				case "error":
					b.error(a);
					break;
				default:
					b.log(a)
				}
			var d = window.opera;
			if (d)
				d.postError(a);
			var c = window.debugService;
			if (c)
				c.trace(a)
		}
		function g(b) {
			if (a._traceEnabled)
				Oc(b)
		}
		function nb(c, d) {
			if (a._logEnabled || a._traceEnabled)
				Oc(c, d);
			b.notify(Mc, c)
		}
		WL.Internal.trace = g;
		WL.Internal.log = nb;
		function m(a) {
			nb(a, "error")
		}
		function Zd(b, c) {
			var a = vb("iframe");
			a.id = c;
			a.src = b;
			document.body.appendChild(a);
			return a
		}
		function vb(b) {
			var a = document.createElement(b);
			a.style.position = "absolute";
			a.style.top = "-1000px";
			a.style.width = "300px";
			a.style.height = "300px";
			return a
		}
		function Fd() {
			var a = null;
			while (a == null) {
				a = "wl_" + Math.floor(Math.random() * 1024 * 1024);
				if (hb(a) != null)
					a = null
			}
			return a
		}
		function hb(a) {
			return document.getElementById(a)
		}
		function Fe(a, b) {
			if (a)
				if (a.innerText)
					a.innerText = b;
				else {
					var c = document.createTextNode(b);
					a.innerHTML = "";
					a.appendChild(c)
				}
		}
		function Pc(a) {
			A(Ue(a), this)
		}
		Pc.prototype = {
			toString : function() {
				var a = this;
				s = (a.scheme != "" ? a.scheme + "//" : "") + a.host
						+ (a.port != "" ? ":" + a.port : "") + a.path;
				return s
			},
			resolve : function() {
				var a = this;
				if (a.scheme == "") {
					var b = window.location.port, c = window.location.host;
					a.scheme = window.location.protocol;
					a.host = c.split(":")[0];
					a.port = b != null ? b : "";
					if (a.path.charAt(0) != "/")
						a.path = Vd(a.host, window.location.href, a.path)
				}
			}
		};
		function Ue(c) {
			var e = c.indexOf(Mb) == 0 ? Mb : c.indexOf(Ob) == 0 ? Ob : "", h = "", i = "", f;
			if (e != "")
				var b = c.substring(e.length + 2), a = b.indexOf("/"), g = a > 0 ? b
						.substring(0, a)
						: b, d = g.split(":"), h = d[0], i = d.length > 1 ? d[1]
						: "", f = a > 0 ? b.substring(a) : "";
			else
				f = c;
			return {
				scheme : e,
				host : h,
				port : i,
				path : f
			}
		}
		function Vd(e, b, h) {
			var d = function(a, c) {
				charIdx = b.indexOf(c);
				a = charIdx > 0 ? a.substring(0, charIdx) : a;
				return a
			};
			b = d(d(b, "?"), "#");
			var f = b.indexOf(e), a = b.substring(f + e.length), g = a
					.indexOf("/"), c = a.lastIndexOf("/");
			a = c >= 0 ? a.substring(g, c) : a;
			return a + "/" + h
		}
		function Me(a) {
			var b = a.indexOf("?");
			if (b > 0)
				a = a.substring(0, b);
			b = a.indexOf("#");
			if (b > 0)
				a = a.substring(0, b);
			return a
		}
		function Q(a, b, d, c) {
			if (a != null) {
				if (c)
					b[Rb] = c;
				if (d)
					a(b);
				else
					window.setTimeout(function() {
						a(b)
					}, 1)
			}
		}
		function xe(a) {
			if (window.JSON)
				return JSON.parse(a);
			else
				return eval("(" + a + ")")
		}
		function sc() {
			return Math.floor((new Date).getTime() / 1000)
		}
		function We(b, c) {
			var d = b.length;
			for ( var a = 0; a < d; a++)
				c(b[a])
		}
		function we(c, b) {
			var a = {};
			a[h] = c;
			a[j] = b;
			return a
		}
		function ub(d, c) {
			var a = {}, b = {};
			a[gb] = d, a[cb] = c;
			b[yc] = a;
			return b
		}
		function Yb(a, b, c) {
			return ub(Y, Kd.replace("METHOD", a).replace("EVENT", b).replace(
					"MESSAGE", c.message))
		}
		function Dd(b) {
			var a = b.split(".");
			return a[0] + "." + a[1]
		}
		function De() {
			var b = navigator.userAgent.toLowerCase();
			a._browser = {
				"firefox" : /firefox/.test(b),
				"firefox1.5" : /firefox\/1\.5/.test(b),
				"firefox2" : /firefox\/2/.test(b),
				"firefox3" : /firefox\/3/.test(b),
				"firefox4" : /firefox\/4/.test(b),
				"ie" : /msie/.test(b) && !/opera/.test(b),
				"ie6" : false,
				"ie7" : false,
				"ie8" : false,
				"ie9" : false,
				"opera" : /opera/.test(b),
				"webkit" : /webkit/.test(b),
				"mobile" : /mobile/.test(b) || /phone/.test(b)
			};
			if (a._browser["ie"]) {
				var c = 0;
				if (document.documentMode)
					c = document.documentMode;
				else if (/msie 7/.test(b))
					c = 7;
				c = Math.min(9, Math.max(c, 6));
				a._browser["ie" + c] = true
			}
		}
		function W(e, c) {
			var f = c != null ? c : {};
			if (e != null) {
				var d = e.split("&");
				for ( var b = 0; b < d.length; b++) {
					var a = d[b].split("=");
					if (a.length == 2)
						f[decodeURIComponent(a[0])] = decodeURIComponent(a[1])
				}
			}
			return f
		}
		function H(b) {
			var a = "";
			if (b != null)
				for ( var c in b) {
					var d = a.length == 0 ? "" : "&", e = b[c];
					a += d + encodeURIComponent(c) + "="
							+ encodeURIComponent(Wd(e))
				}
			return a
		}
		function Wd(a) {
			if (a instanceof Date) {
				var b = function(a, b) {
					switch (b) {
					case 2:
						return a < 10 ? "0" + a : a;
					case 3:
						return (a < 10 ? "00" : a < 100 ? "0" : "") + a
					}
				};
				return a.getUTCFullYear() + "-" + b(a.getUTCMonth() + 1, 2)
						+ "-" + b(a.getUTCDate(), 2) + "T"
						+ b(a.getUTCHours(), 2) + ":" + b(a.getUTCMinutes(), 2)
						+ ":" + b(a.getUTCSeconds(), 2) + "."
						+ b(a.getUTCMilliseconds(), 3) + "Z"
			}
			return "" + a
		}
		function ne(b) {
			var d = b.indexOf("?") + 1, c = b.indexOf("#") + 1, a = {};
			if (d > 0) {
				var e = c > d ? c - 1 : b.length;
				a = W(b.substring(d, e), a)
			}
			if (c > 0)
				a = W(b.substring(c), a);
			return a
		}
		function jc(a, b) {
			return a + (a.indexOf("?") < 0 ? "?" : "&") + H(b)
		}
		function Hd(a) {
			switch (typeof a) {
			case R:
				return a;
			case Oe:
				return !!a;
			case c:
				return a.toLowerCase() === "true";
			default:
				return false
			}
		}
		function kb(a, c, d) {
			if (a instanceof Array)
				for ( var b = 0; b < a.length; b++)
					Gc(a[b], c[b], d);
			else
				Gc(a, c, d)
		}
		function Gc(c, a, b) {
			uc(c, a, b);
			if (a.type === "properties")
				o(c, a.properties, b)
		}
		function uc(f, e, b) {
			var d = e.name, a = typeof f, g = e.type;
			if (a === "undefined" || f == null)
				if (e.optional)
					return;
				else
					throw pb(d, b);
			switch (g) {
			case "string":
			case "url":
				if (a !== c)
					throw G(d, b);
				if (!e.optional && N(f) === "")
					throw pb(d, b);
				break;
			case "properties":
				if (a != B)
					throw G(d, b);
				break;
			case "function":
				if (a != k)
					throw G(a, b);
				break;
			case "dom":
				if (a == c) {
					if (hb(f) == null)
						throw new Error(xd.replace("METHOD", b).replace(
								"PARAM", d))
				} else if (a != B)
					throw G(d, b);
				break;
			case "string_or_array":
				if (a !== c && !(f instanceof Array))
					throw G(a, b);
				break;
			default:
				if (a !== g)
					throw G(d, b)
			}
			if (e.allowedValues != null)
				Pd(f, e.allowedValues, e.caseSensitive, d, b)
		}
		function o(g, c, f) {
			var d = g || {};
			for ( var b = 0; b < c.length; b++) {
				var a = c[b], e = d[a.name] || d[a.altName];
				uc(e, a, f)
			}
		}
		function Pd(d, a, e, f, h) {
			var g = typeof a[0] === c;
			for ( var b = 0; b < a.length; b++)
				if (g && !e) {
					if (d.toLowerCase() === a[b].toLowerCase())
						return
				} else if (d === a[b])
					return;
			throw pd(f, h)
		}
		function G(a, b) {
			return new Error(Zc.replace("METHOD", b).replace("PARAM", a))
		}
		function pb(a, b) {
			return new Error(md.replace("METHOD", b).replace("PARAM", a))
		}
		function pd(a, b) {
			return new Error(ld.replace("METHOD", b).replace("PARAM", a))
		}
		function mc(b, d, c) {
			if (b)
				for ( var a = 0; a < c && a < b.length; a++)
					if (d === typeof b[a])
						return b[a];
			return undefined
		}
		var n = function(b, c, a) {
			this._name = b;
			this._op = c;
			this._uplinkPromise = a;
			this._isCompleted = false;
			this._listeners = []
		};
		n.prototype = {
			then : function(d, e, c) {
				var b = new n(null, null, this), a = {};
				a[p] = d;
				a[w] = e;
				a[E] = c;
				a.chainedPromise = b;
				this._listeners.push(a);
				return b
			},
			cancel : function() {
				if (this._isCompleted)
					return;
				if (this._uplinkPromise && !this._uplinkPromise._isCompleted)
					this._uplinkPromise.cancel();
				else {
					var a = this._op ? this._op.cancel : null;
					if (typeof a === k)
						this._op.cancel();
					else
						this.onError(ub(wc, je.replace("METHOD", this
								._getName())))
				}
			},
			_getName : function() {
				if (this._name)
					return this._name;
				if (this._op && typeof this._op._getName === k)
					return this._op._getName();
				if (this._uplinkPromise)
					return this._uplinkPromise._getName();
				return ""
			},
			_onEvent : function(b, a) {
				if (this._isCompleted)
					return;
				this._isCompleted = a !== E;
				this._notify(b, a)
			},
			_notify : function(b, a) {
				var c = this;
				We(this._listeners, function(g) {
					var h = g[a], d = g.chainedPromise, f = a !== E;
					if (h)
						try {
							var e = h.apply(g, b);
							if (f && e && e.then) {
								d._op = e;
								e.then(function(a) {
									d[p](a)
								}, function(a) {
									d[w](a)
								}, function(a) {
									d[E](a)
								})
							}
						} catch (i) {
							if (f)
								d.onError(Yb(c._getName(), a, i))
						}
					else if (f)
						d[a].apply(d, b)
				})
			}
		};
		n.prototype[p] = function() {
			this._onEvent(arguments, p)
		};
		n.prototype[w] = function() {
			this._onEvent(arguments, w)
		};
		n.prototype[E] = function() {
			this._onEvent(arguments, E)
		};
		function V(e, d, b, f) {
			var a = new n(e, null, null), c = d ? p : w;
			window.setTimeout(function() {
				a[c](f)
			}, 1);
			if (typeof b === k)
				a.then(function(a) {
					b(a)
				});
			return a
		}
		var fb = [ e, lb, f, K, C, r, h, j ], lc = "login", hd = "loginstatus", Z = "auth.response";
		function Nd() {
			a._urlParams = ne(window.location.href);
			a._pageState = W(a._urlParams[Rb])
		}
		function oc() {
			var b = new M(Ic);
			b.load();
			var c = a._urlParams, f = a._pageState;
			if (f[r])
				b.set(r, f[r]);
			var m = c[e] != null, g = b.get(e) != null || m, p = g ? l : D, o = sc();
			if (f[I] === ab) {
				for ( var k = 0; k < fb.length; k++) {
					var i = fb[k];
					if (c[i])
						b.set(i, c[i])
				}
				if (m) {
					b.set(C, o + parseInt(c[K]));
					b.remove(h);
					b.remove(j)
				} else if (!g)
					if (c[h] === wb)
						p = Ac
			} else {
				var n = qd(b);
				if (n) {
					b.set(h, be);
					b.set(j, n)
				} else if (g && b.get(h) == null)
					b.set(C, o + parseInt(b.get(K)))
			}
			b.set(d, p);
			b.save()
		}
		function te() {
			var b = a._pageState, c = b[Pb], e = b[Cb] === "true";
			a._logEnabled = true;
			a._traceEnabled = b[Sb] || a._urlParams[Sb];
			a._secureCookie = e;
			wd();
			if (c === Fc || c === Hb) {
				oc();
				if (c === Hb && a._browser.ie)
					document.location = b[q];
				else
					window.close()
			} else if (c === Lb)
				oc();
			else {
				bb(ze);
				var d = window.wlAsyncInit;
				if (d && typeof d === k)
					d.call()
			}
		}
		function Md(a, b) {
			if (!a)
				a = Me(window.location.href);
			return Sc(a, window.location.hostname, b)
		}
		function Sc(f, e, d) {
			var b = new Pc(f);
			b.resolve();
			var e = e.split(":")[0].toLowerCase(), c = b.host.toLowerCase();
			a._domain = a._domain || c;
			if (a._isHttps && b.scheme == Ob)
				throw new Error(ce.replace("METHOD", d));
			return b.toString()
		}
		function qd(a) {
			var j = a.get(e) != null, g = a.get(h) != null, i = a.get(f) != null, c = a
					.get(K) != null, d = a.get(t) != null, b = null;
			if (!(j && i && c) && !g) {
				m(Wb);
				b = Wb
			}
			if (!d) {
				m(Vb);
				b = Vb
			}
			return b
		}
		function Ee() {
			ic = a._browser.ie ? 2000 : 4000;
			Nd();
			te()
		}
		function od(b) {
			a[ob] = "Web/" + Dd(a._version);
			a._redirect_uri = Md(b[q], "WL.init");
			a._response_type = (b[I] || ab).toLowerCase()
		}
		function Ae() {
			return true
		}
		function Wc() {
			return true
		}
		function ee() {
			var b = a._pendingLogin;
			if (b != null) {
				b.cancel();
				a._pendingLogin = null
			}
			return true
		}
		function ae(b, a) {
			return new Nb(lc, b, a)
		}
		function id(b, a) {
			return new Nb(hd, b, a)
		}
		function me(d) {
			vc();
			var b = vb("iframe"), c = rc(), e = "/oauth20_logout.srf?ts=";
			b.src = "//" + c + e + (new Date).getTime();
			document.body.appendChild(b);
			a.logoutFrame = b;
			window.setTimeout(function() {
				vc();
				d()
			}, 30000)
		}
		function vc() {
			if (a.logoutFrame != null) {
				document.body.removeChild(a.logoutFrame);
				a.logoutFrame = null
			}
		}
		function Qc(a) {
			o(a, [ {
				name : jb,
				allowedValues : [ gc, Id ],
				type : c,
				optional : true
			}, {
				name : ib,
				allowedValues : [ he, ve, pe, Db, fc, Jb ],
				type : c,
				optional : true
			} ], "WL.ui(name:'signin')");
			a[jb] = a[jb] || gc;
			a[ib] = a[ib] || Db
		}
		function vd(f) {
			var d = f[ib], e = f[jb], g = a._locale, b = g.indexOf("ar") > -1
					|| g.indexOf("he") > -1 ? "RTL" : "LTR", h = "cursor:pointer;background-color:transparent;border:solid 0px;display:inline-block;overflow:hidden;white-space:nowrap;padding:0px;width:auto;", c = "margin:0px;padding:0px;border-width:0px;vertical-align:middle;background-attachment:scroll;display:inline-block;white-space:nowrap;", k = xb(
					d, b, e, "left")
					+ c, i = xb(d, b, e, "center") + c, j = xb(d, b, e, "right")
					+ c;
			return '<button style="' + h + '"><span style="' + k
					+ '"></span><span style="' + i + '"><span style="' + fd(b)
					+ '"></span></span><span style="' + j
					+ '"></span></button>'
		}
		function fd(g) {
			var b = a._browser, j = b.ie6 || b.ie7, k = b.ie8 || b.ie9, f = b.chrome
					|| b.safari ? "padding:2px 3px;margin:0px;"
					: "padding:1px 3px;margin:0px;", h = "font-family: Segoe UI, Verdana, Tahoma, Helvetica, Arial, sans-serif;", e = "direction:"
					+ g.toLowerCase() + ";", d = "text-decoration:none;color:#3975a0;display:inline-block;", c = "150";
			switch (a._locale) {
			case "ar-ploc-sa":
				if (!j)
					c = "170";
				break;
			case "te":
			case "ja-ploc-jp":
				if (b.ie)
					c = "190"
			}
			var i = "height:18px;font-size:9pt;font-weight:bold;line-height:"
					+ c + "%;";
			return f + e + d + h + i
		}
		function xb(a, b, i, g) {
			a = a == Db ? fc : a;
			var h = a + "_" + b + "_" + i, e, d, c, f = "background: url({imgpath}/signincontrol/{image}.png) scroll {repeat} 0px {vpos}; height: 22px; width: {width};";
			switch (g) {
			case "left":
				e = a === Jb || b === "RTL" ? "3px" : "25px";
				d = b === "LTR" ? "0px" : "-44px";
				c = "no-repeat";
				break;
			case "center":
				e = "auto";
				d = "-22px";
				c = "repeat-x";
				break;
			case "right":
				e = a === Jb || b === "LTR" ? "3px" : "25px";
				d = b === "LTR" ? "-44px" : "0px";
				c = "no-repeat"
			}
			return f.replace("{imgpath}", Je()).replace("{image}", h).replace(
					"{vpos}", d).replace("{width}", e).replace("{repeat}", c)
		}
		Ib.prototype.setText = function(d) {
			if (this._element.childNodes.length == 0) {
				Ub(this);
				return
			}
			if (d != this._text) {
				var c = a._browser, b = this._element.childNodes[0], e = b.childNodes[1];
				Fe(e.childNodes[0], d);
				this._text = d;
				if (c.ie6 || c.ie7) {
					e.style.width = "auto";
					b.style.width = "auto";
					var h = b.childNodes[0].clientWidth, f = b.childNodes[1].clientWidth, g = b.childNodes[2].clientWidth, i = h
							+ f + g;
					b.style.width = i + "px";
					if (c.ie6) {
						b.childNodes[0].style.width = h + "px";
						b.childNodes[1].style.width = f + "px";
						b.childNodes[2].style.width = g + "px"
					}
				}
			}
		};
		function Xc(a, b) {
			a._button = b;
			Be(b, "click", Uc("click", a, a.onClick))
		}
		function Ub(a) {
			var b = a._button;
			if (b) {
				Ce(b, "click", ad("click", a));
				delete a._button
			}
		}
		function Be(a, b, c) {
			if (a.addEventListener)
				a.addEventListener(b, c, false);
			else if (a.attachEvent)
				a.attachEvent("on" + b, c)
		}
		function Ce(a, b, c) {
			if (a.removeEventListener)
				a.removeEventListener(b, c, false);
			else if (a.detachEvent)
				a.detachEvent("on" + b, c)
		}
		M.prototype.addCookieChanged = function(a) {
			this._listeners.push(a);
			this.startMonitor()
		};
		M.prototype.startMonitor = function() {
			this._refreshInterval = 300;
			if (!this._cookieWatcher) {
				g("Started monitoring cookie: " + this._cookieName);
				this._cookieWatcher = window.setInterval(i(this, this.load),
						this._refreshInterval)
			}
		};
		M.prototype.stopMonitor = function() {
			if (this._cookieWatcher) {
				g("Stopped monitoring cookie: " + this._cookieName);
				window.clearInterval(this._cookieWatcher);
				this._cookieWatcher = null
			}
		};
		function Bc() {
			var b = a._domain || window.location.hostname.split(":")[0];
			return b
		}
		var Nb = function(d, b, c) {
			var a = this;
			a._method = d;
			a._completed = false;
			a._requestFired = false;
			a._properties = b;
			a._callback = c;
			a._authMonitor = i(a, a._onAuthChanged);
			a.execute = a._method == lc ? a._login : a._getLoginStatus
		};
		Nb.prototype = {
			cancel : function() {
				this._onComplete({
					error : wc,
					error_description : sd
				})
			},
			_login : function() {
				var c = this;
				c._requestTs = (new Date).getTime();
				var e = a._browser.mobile, d = e && a._browser.ie, h = e ? Hb
						: Fc, f = Hc(h, c._properties.normalizedScope,
						nc(c._properties), c._requestTs, d);
				if (d)
					document.location = f;
				else {
					c._popup = Re(f);
					g("AuthRequest-login: popup is opened. " + c._popup);
					c._popupWatcher = window.setInterval(i(c, c._checkPopup),
							3000);
					b.subscribe(Z, c._authMonitor)
				}
				c._promise = new n("WL.login", null, null);
				return c._promise
			},
			_getLoginStatus : function() {
				Ud(i(this, this._fireStatusRequest));
				this._timeout = window
						.setTimeout(i(this, this._onTimedOut), ud);
				this._promise = new n("WL.getLoginStatus", null, null);
				return this._promise
			},
			_fireStatusRequest : function() {
				if (!this._requestFired) {
					this._requestTs = (new Date).getTime();
					var c = Hc(Lb, a._authScope, nc(), this._requestTs, false);
					this._statusFrame = Zd(c);
					b.subscribe(Z, this._authMonitor);
					this._requestFired = true
				}
			},
			_onAuthChanged : function() {
				var b = a._session.tryGetResponse(
						this._properties.normalizedScope, this._requestTs);
				if (b != null)
					this._onComplete(b)
			},
			_onTimedOut : function() {
				this._onComplete({
					error : Dc,
					error_description : nd
				})
			},
			_checkPopup : function() {
				try {
					if (this._popup === null)
						this._onComplete({
							error : wb,
							error_description : zd
						});
					else if (this._popup.closed)
						this._popup = null
				} catch (a) {
					g("AuthRequest-checkPopup-error: " + a)
				}
			},
			_onComplete : function(a) {
				if (!this._completed) {
					this._completed = true;
					this._dispose();
					this._callback(this._properties, a);
					if (a[h])
						this._promise[w](a);
					else
						this._promise[p](a)
				}
			},
			_dispose : function() {
				g("AuthRequest: dispose " + this._method);
				if (this._timeout) {
					window.clearTimeout(this._timeout);
					this._timeout = null
				}
				if (this._statusFrame != null) {
					document.body.removeChild(this._statusFrame);
					this._statusFrame = null
				}
				if (this._popupWatcher) {
					window.clearInterval(this._popupWatcher);
					this._popupWatcher = null
				}
				b.unsubscribe(Z, this._authMonitor)
			}
		};
		function Re(p) {
			var c = 525, b = 475, e, d;
			if (a._browser.ie) {
				var k = window.screenLeft, l = window.screenTop, f = document.documentElement, i = 30;
				d = k + (f.clientWidth - c) / 2;
				e = l + (f.clientHeight - b) / 2 - i
			} else {
				var n = window.screenX, o = window.screenY, j = window.outerWidth, h = window.outerHeight;
				d = n + (j - c) / 2;
				e = o + (h - b) / 2
			}
			var m = [ "width=" + c, "height=" + b, "top=" + e, "left=" + d,
					"status=no", "resizable=yes", "toolbar=no", "menubar=no",
					"scrollbars=yes" ], g = window
					.open(p, "oauth", m.join(","));
			g.focus();
			return g
		}
		function Hc(d, k, g, i, j) {
			var c = {};
			c[Pb] = d;
			c[r] = i;
			if (j)
				c[q] = window.location.href;
			if (a.trace)
				c[Sb] = true;
			var e = d === Lb ? ab : a._response_type;
			c[I] = e;
			c[Cb] = a._secureCookie;
			var l = H(c), b = {};
			b[t] = a._session.get(t);
			b[Pb] = d;
			b[Pe] = a._locale;
			b[q] = g;
			b[I] = e;
			b[f] = k;
			b[Rb] = l;
			var h = rc(), m = "https://" + h + "/oauth20_authorize.srf?" + H(b);
			return m
		}
		function nc(c) {
			var b = c != null ? c[q] : null;
			return b != null && b != "" ? b : a._redirect_uri
		}
		L.prototype.initPlatformSpecific = function() {
			var a = this._state[d];
			this._statusChecked = a !== D && a !== u;
			this._cookie.addCookieChanged(i(this, this.onCookieChanged))
		};
		L.prototype.onCookieChanged = function() {
			var c = this._state, a = this._cookie.getStates();
			this._state = a;
			g("AuthSession: cookie changed. Has token: " + (a[e] != null));
			this._statusChecked = a[d] !== u;
			if (c[e] != a[e] || c[h] != a[h] || c[r] != a[r]) {
				b.notify(Z);
				delete a[h];
				delete a[j];
				this._stateDirty = true
			}
			if (c[d] != a[d])
				this.onStatusChanged(c[d], a[d]);
			if (c[e] != a[e]) {
				b.notify(T, this.getNormalStatus());
				if (a[e])
					this.scheduleRefresh();
				else
					this.cancelRefresh()
			}
			this.save()
		};
		L.prototype.getStatus = function() {
			var b = null, c = this._state[d];
			if (c === l) {
				var g = this.tokenExpiresIn();
				if (g <= 10) {
					c = this._statusChecked ? D : u;
					this.updateStatus(c);
					window.setTimeout(function() {
						a.getLoginStatus({
							internal : true
						}, true)
					}, 30000)
				} else {
					if (g < 60)
						c = mb;
					b = {};
					b[e] = this._state[e];
					b[lb] = this._state[lb];
					b[f] = this._state[f].split(" ");
					b[K] = g;
					b[C] = this._state[C]
				}
			} else if (!this._statusChecked)
				c = u;
			return {
				status : c,
				session : b
			}
		};
		L.prototype.tryGetResponse = function(b, c) {
			g("AuthSession.tryGetResponse: requestTs = " + c + " scope = " + b);
			var a = this.getStatus(), k = a[d];
			session = a[Jc];
			if (k == u || k == mb)
				return null;
			if (c === undefined)
				if (b)
					return session && xc(session[f], b) ? a : null;
				else
					return a;
			var e = this._state, l = parseInt(e[r]), i = e[h], m = e[j];
			if (l >= c) {
				if (session && xc(session[f], b))
					return a;
				if (i)
					return we(i, m);
				if (!b)
					return a
			}
			return null
		};
		function rb(a) {
			return a === u ? D : a === mb ? l : a
		}
		function le(a) {
			if (Cd(a))
				return;
			if (Od(a))
				return;
			if (Bd(a))
				return;
			var b = {};
			b[gb] = Y;
			b[cb] = kd;
			a.onCompleted(b)
		}
		function Se() {
			return window.XMLHttpRequest
					&& (new XMLHttpRequest).withCredentials !== undefined
		}
		function rc() {
			return Ie
		}
		function se() {
			return qe
		}
		a.jsonp = {};
		WL.Internal.jsonp = a.jsonp;
		function Cd(c) {
			var i = document.getElementsByTagName("HEAD")[0], b = document
					.createElement("SCRIPT"), d = qc(c._properties, d, [ x, z ]), f = c._id, g = ec();
			if (g != null)
				d[e] = g;
			d[x] = Rc + f;
			d[ac] = "true";
			var h = jc(c._url, d);
			if (h.length > ic)
				return false;
			c.scriptTag = b;
			a.jsonp[f] = function(a) {
				tb(f, b);
				c.onCompleted(a)
			};
			Yd(b, c);
			b.setAttribute("async", "async");
			b.type = "text/javascript";
			b.src = h;
			i.appendChild(b);
			window.setTimeout(function() {
				if (c._completed)
					return;
				tb(f, b)
			}, 30000);
			return true
		}
		function Yd(b, c) {
			if (a._browser.ie)
				b.attachEvent("onreadystatechange", function(a) {
					Gb(a, c)
				});
			else {
				b.readyState = "complete";
				b.addEventListener("load", function(a) {
					Gb(a, c)
				}, false);
				b.addEventListener("error", function(a) {
					Gb(a, c)
				}, false)
			}
		}
		function Gb(d, c) {
			if (c._completed)
				return;
			var b = d.srcElement || d.currentTarget;
			if (!b.readyState)
				b = d.currentTarget;
			if (b.readyState != "complete" && b.readyState != "loaded")
				return;
			var f = c._id;
			failure = d.type == "error" || a.jsonp[f] != null;
			if (failure) {
				tb(f, c.scriptTag);
				var e = {};
				e[gb] = rd;
				e[cb] = Zb;
				c.onCompleted({
					error : e
				})
			}
		}
		function tb(b, c) {
			delete a.jsonp[b];
			document.getElementsByTagName("HEAD")[0].removeChild(c)
		}
		function Bd(b) {
			Ne();
			if (a._browser.flashVersion < 9)
				return false;
			a.xdrFlash.send(b);
			return true
		}
		a.xdrFlash = {
			_id : "",
			_status : Bb,
			_flashObject : null,
			_requests : {},
			_pending : [],
			init : function() {
				if (this._status != Bb)
					return;
				this._status = ed;
				var c = vb("div");
				c.id = "wl_xdr_container";
				document.body.appendChild(c);
				this._id = Fd();
				var b = td, d = a[S] + "XDR.swf";
				b = b.replace(/{url}/g, d);
				b = b.replace(/{id}/g, this._id);
				b = b.replace(/{variables}/g, "domain=" + document.domain);
				c.innerHTML = b
			},
			onReady : function(b) {
				if (b) {
					if (a._browser.firefox)
						this._flashObject = document.embeds[this._id];
					else
						this._flashObject = hb(this._id);
					this._status = Xb
				} else
					this._status = de;
				while (this._pending.length > 0)
					this.send(this._pending.shift())
			},
			onRequestCompleted : function(b, e, c, f) {
				var d = a.xdrFlash._requests[b];
				delete a.xdrFlash._requests[b];
				yb(d, e, ye(c), f)
			},
			send : function(a) {
				if (this._status < Xb) {
					this._pending.push(a);
					if (this._status == Bb)
						bb(i(this, this.init));
					return
				}
				if (this._flashObject != null) {
					this._requests[a._id] = a;
					var b = tc(a);
					this.invoke("send", [ a._id, b.url, b.method, b.body ])
				} else
					yb(a, 0, null, jd)
			},
			invoke : function(d, a) {
				a = a || [];
				var c = window.__flash__argumentsToXML(a, 0), e = '<invoke name="'
						+ d + '" returntype="javascript">' + c + "</invoke>", b = this._flashObject
						.CallFunction(e);
				if (b == null)
					return null;
				return eval(b)
			}
		};
		WL.Internal.xdrFlash = a.xdrFlash;
		function ye(a) {
			return a ? a.replace(/\r/g, " ").replace(/\n/g, " ") : a
		}
		var td = "<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='https://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0' width='300' height='300' id='{id}' name='{id}' type='application/x-shockwave-flash' data='{url}'>"
				+ "<param name='movie' value='{url}'></param>"
				+ "<param name='allowScriptAccess' value='always'></param>"
				+ "<param name='FlashVars' value='{variables}'></param>"
				+ "<embed play='true' menu='false' swLiveConnect='true' allowScriptAccess='always' type='application/x-shockwave-flash' FlashVars='{variables}' src='{url}' width='300' height='300' name='{id}'></embed>"
				+ "</object>";
		function wd() {
			a._isHttps = document.location.protocol.toLowerCase() == Mb
		}
		function Ne() {
			if (a._browser.flash !== undefined)
				return;
			var b = 0;
			try {
				if (a._browser.ie) {
					var k = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7"), f = k
							.GetVariable("$version"), i = f.split(" "), g = i[1], d = g
							.split(",");
					b = d[0]
				} else if (navigator.plugins && navigator.plugins.length > 0)
					if (navigator.plugins["Shockwave Flash 2.0"]
							|| navigator.plugins["Shockwave Flash"]) {
						var j = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0"
								: "", e = navigator.plugins["Shockwave Flash"
								+ j].description, h = e.split(" "), c = h[2]
								.split(".");
						b = c[0]
					}
			} catch (l) {
			}
			a._browser.flashVersion = b;
			a._browser.flash = b >= 8
		}
		function ze() {
			if (a._documentReady === undefined)
				a._documentReady = (new Date).getTime()
		}
		function Ud(b) {
			bb(function() {
				if (a._browser.firefox
						&& (!a._documentReady || (new Date).getTime()
								- a._documentReady < 1000))
					window.setTimeout(b, 1000);
				else
					b()
			})
		}
		function bb(b) {
			if (document.body)
				switch (document.readyState) {
				case "complete":
					b();
					return;
				case "loaded":
					if (a._browser.webkit) {
						b();
						return
					}
					break;
				case "interactive":
				case undefined:
					if (a._browser.firefox || a._browser.webkit) {
						b();
						return
					}
				}
			if (document.addEventListener) {
				document.addEventListener("DOMContentLoaded", b, false);
				document.addEventListener("load", b, false)
			} else if (window.attachEvent)
				window.attachEvent("onload", b);
			if (a._browser.ie)
				document.attachEvent("onreadystatechange", function() {
					if (document.readyState === "complete") {
						document.detachEvent("onreadystatechange",
								arguments.callee);
						b()
					}
				})
		}
		function Le(b, a) {
			b.innerHTML = a
		}
		var O = {
			connect : "Connect",
			signIn : "Sign in",
			signOut : "Sign out",
			login : "Log in",
			logout : "Log out"
		};
		a._locale = "en";
		var Ie = "login.live.com";
		a._version = "5.0.3267.0402";
		var qe = "https://apis.live.net/v5.0/";
		a[S] = "//js.live.net/v5.0/";
		a.onloadInit()
	}
})();