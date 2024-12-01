--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2 (Debian 17.2-1.pgdg120+1)
-- Dumped by pg_dump version 17.2 (Debian 17.2-1.pgdg120+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: coupon; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.coupon (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    status integer NOT NULL
);


ALTER TABLE public.coupon OWNER TO app;

--
-- Name: coupon_id_seq; Type: SEQUENCE; Schema: public; Owner: app
--

CREATE SEQUENCE public.coupon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.coupon_id_seq OWNER TO app;

--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO app;

--
-- Name: product; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.product (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    price integer NOT NULL,
    status integer NOT NULL
);


ALTER TABLE public.product OWNER TO app;

--
-- Name: product_id_seq; Type: SEQUENCE; Schema: public; Owner: app
--

CREATE SEQUENCE public.product_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_id_seq OWNER TO app;

--
-- Name: tax_number; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.tax_number (
    id integer NOT NULL,
    pattern character varying(255) NOT NULL,
    tax integer NOT NULL
);


ALTER TABLE public.tax_number OWNER TO app;

--
-- Name: tax_number_id_seq; Type: SEQUENCE; Schema: public; Owner: app
--

CREATE SEQUENCE public.tax_number_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tax_number_id_seq OWNER TO app;

--
-- Data for Name: coupon; Type: TABLE DATA; Schema: public; Owner: app
--

COPY public.coupon (id, name, status) FROM stdin;
1	DIS15	1
2	DIS25	1
3	DIS6	1
\.


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: app
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20241129193548	2024-11-29 19:36:02	4
\.


--
-- Data for Name: product; Type: TABLE DATA; Schema: public; Owner: app
--

COPY public.product (id, name, price, status) FROM stdin;
1	iPhone	10000	1
2	Headphones	2000	1
3	Case	1000	1
\.


--
-- Data for Name: tax_number; Type: TABLE DATA; Schema: public; Owner: app
--

COPY public.tax_number (id, pattern, tax) FROM stdin;
1	DEXXXXXXXXX	19
2	ITXXXXXXXXXXX	22
3	GRXXXXXXXXX	24
4	FRYYXXXXXXXXX	20
\.


--
-- Name: coupon_id_seq; Type: SEQUENCE SET; Schema: public; Owner: app
--

SELECT pg_catalog.setval('public.coupon_id_seq', 1, false);


--
-- Name: product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: app
--

SELECT pg_catalog.setval('public.product_id_seq', 1, false);


--
-- Name: tax_number_id_seq; Type: SEQUENCE SET; Schema: public; Owner: app
--

SELECT pg_catalog.setval('public.tax_number_id_seq', 1, false);


--
-- Name: coupon coupon_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.coupon
    ADD CONSTRAINT coupon_pkey PRIMARY KEY (id);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: product product_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_pkey PRIMARY KEY (id);


--
-- Name: tax_number tax_number_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.tax_number
    ADD CONSTRAINT tax_number_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--
