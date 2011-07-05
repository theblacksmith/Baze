/**
 * This is a utility class for holding criteria information for a query.
 *
 * BasePeer constructs SQL statements based on the values in this class.
 *
 * @author Hans Lellelid <hans@xmpl.org> (Propel)
 * @author Kaspars Jaudzems <kaspars.jaudzems@inbox.lv> (Propel)
 * @author Frank Y. Kim <frank.kim@clearink.com> (Torque)
 * @author John D. McNally <jmcnally@collab.net> (Torque)
 * @author Brett McLaughlin <bmclaugh@algx.net> (Torque)
 * @author Eric Dobbs <eric@dobbse.net> (Torque)
 * @author Henning P. Schmiedehausen <hps@intermeta.de> (Torque)
 * @author Sam Joseph <sam@neurogrid.com> (Torque)
 * @version $Revision: 330 $
 * @package propel.util
 */
function Criteria(){

    var ignoreCase = false;
    var selectModifiers = [];
    var joins = [];

    /**
     * Primary storage of criteria data.
     * @var array
     */
    var map = [];

    /**
     * Creates a new instance with the default capacity which corresponds to
     * the specified database.
     *
     * @param dbName The dabase name.
     */
    function __construct(dbName)
    {
        this.setDbName(dbName);
        this.originalDbName = dbName;
    }

	/**
	 * Get the criteria map.
	 * @return array
	 */
	this.getMap = function getMap()
	{
		return map;
	};

    /**
     * Brings this criteria back to its initial state, so that it
     * can be reused as if it was new. Except if the criteria has grown in
     * capacity, it is left at the current capacity.
     * @return void
     */
    this.clear = function clear()
	{
        this.map = [];
        this.ignoreCase = false;
        this.singleRecord = false;
        this.selectModifiers = [];
        this.selectColumns = [];
        this.orderByColumns = [];
        this.groupByColumns = [];
        this.having = null;
        this.asColumns = [];
        this.joins = [];
        this.dbName = this.originalDbName;
        this.offset = 0;
        this.limit = -1;
        this.blobFlag = null;
        this.aliases = null;
        this.useTransaction = false;
    };

    /**
     * Get the keys for the criteria map.
     * @return array
     */
    this.keys = function keys()
    {
        return array_keys(this.map);
    };

    /**
     * Does this Criteria object contain the specified key?
     *
     * @param string column [table.]column
     * @return boolean True if this Criteria object contain the specified key.
     */
    this.containsKey = function(column)
    {
        // must use array_key_exists() because the key could
        // exist but have a NULL value (that'd be valid).
        return array_key_exists(column, this.map);
    };

    /**
     * Method to return criteria related to columns in a table.
     *
		 * @param string column Column name.
     * @return A Criterion or null if column is invalid.
     */
    this.getCriterion = function(column)
    {
			if (isset(this.map[column])) {
		    	return this.map[column];
			}
    };

    /**
     * Method to return criterion that is not added automatically
     * to this Criteria.  This can be used to chain the
     * Criterions to form a more complex where clause.
     *
     * @param column String full name of column (for example TABLE.COLUMN).
     * @param mixed value
     * @param string comparison
     * @return A Criterion.
     */
    this.getNewCriterion = function(column, value, comparison)
    {
        return new Criterion(this, column, value, comparison);
    };


    /**
     * This method adds a new criterion to the list of criterias.
     * If a criterion for the requested column already exists, it is
     * replaced. If is used as follow:
     *
     * <p>
     * <code>
     * crit = new Criteria();
     * crit.add(&quot;column&quot;,
     *                                      &quot;value&quot;
     *                                      &quot;Criteria::GREATER_THAN&quot;);
     * </code>
     *
     * Any comparison can be used.
     *
     * The name of the table must be used implicitly in the column name,
     * so the Column name must be something like 'TABLE.id'. If you
     * don't like this, you can use the add(table, column, value) method.
     *
     * @param string critOrColumn The column to run the comparison on, or Criterion object.
     * @param mixed value
     * @param string comparison A String.
     *
     * @return A modified Criteria object.
     */
    this.add = function add(p1, value, comparison)
    {
        if (p1 instanceof Criterion) {
            c = p1;
            this.map[c.getTable() + '.' + c.getColumn()] = c;
        } else {
            column = p1;
            this.map[column] = new Criterion(this, column, value, comparison);
        }
        return this;
    };

    /**
     * This is the way that you should add a straight (inner) join of two tables.  For
     * example:
     *
     * <p>
     * AND PROJECT.PROJECT_ID=FOO.PROJECT_ID
     * <p>
     *
     * left = PROJECT.PROJECT_ID
     * right = FOO.PROJECT_ID
     *
     * @param string left A String with the left side of the join.
     * @param string right A String with the right side of the join.
		 * @param string operator A String with the join operator e.g. LEFT JOIN, ...
     * @return Criteria A modified Criteria object.
     */
    this.addJoin = function addJoin(left, right, operator)
    {
        this.joins.push(new Join(left, right, operator));

        return this;
    };

    /**
     * Get the array of Joins.  This method is meant to
     * be called by BasePeer.
     * @return an array which contains objects of type Join,
     *         or an empty array if the criteria does not contains any joins
     */
    this.getJoins = function getJoins()
    {
        return joins;
    };


    /**
     * Sets ignore case.
     *
     * @param boolean b True if case should be ignored.
     * @return A modified Criteria object.
     */
    this.setIgnoreCase = function(b)
    {
        this.ignoreCase = (b == true);
        return this;
    };

    /**
     * Is ignore case on or off?
     *
     * @return boolean True if case is ignored.
     */
    this.isIgnoreCase = function isIgnoreCase()
    {
        return this.ignoreCase;
    };


    /**
     * Remove an object from the criteria.
     *
     * @param string key A string with the key to be removed.
     * @return mixed The removed value.
     */
    this.remove = function remove(key)
    {
        c = isset(this.map[key]) ? this.map[key] : null;
        unset(this.map[key]);
        if (c instanceof Criterion) {
            return c.getValue();
        }
        return c;
    };


    /**
     * Returns the size (count) of this criteria.
     * @return int
     */
    this.size = function size()
    {
        return count(this.map);
    };


    /**
     * This method adds a new criterion to the list of criterias.
     * If a criterion for the requested column already exists, it is
     * "AND"ed to the existing criterion.
      *
     * addAnd(column, value, comparison)
     * <code>
     * crit = orig_crit.addAnd(&quot;column&quot;,
     *                                      &quot;value&quot;
     *                                      &quot;Criterion::GREATER_THAN&quot;);
     * </code>
     *
     * addAnd(column, value)
     * <code>
     * crit = orig_crit.addAnd(&quot;column&quot;, &quot;value&quot;);
     * </code>
     *
     * addAnd(Criterion)
     * <code>
     * crit = new Criteria();
     * c = crit.getNewCriterion(BasePeer::ID, 5, Criteria::LESS_THAN);
     * crit.addAnd(c);
     * </code>
     *
     * Any comparison can be used, of course.
     *
     *
     * @return Criteria A modified Criteria object.
     */
    this.addAnd = function(p1, p2, p3)
    {
        if (p3 !== null) {
            // addAnd(column, value, comparison)
            oc = this.getCriterion(p1);
            nc = new Criterion(this, p1, p2, p3);
            if (oc === null) {
                this.map[p1] = nc;
            } else {
                oc.addAnd(nc);
            }
        } else if (p2 !== null) {
            // addAnd(column, value)
            this.addAnd(p1, p2, Criteria.EQUAL);
        } else if (p1 instanceof Criterion) {
            // addAnd(Criterion)
            c = p1;
            oc = this.getCriterion(c.getTable() + '.' + c.getColumn());
            if (oc === null) {
                this.add(c);
            } else {
                oc.addAnd(c);
            }
        } else if (p2 === null && p3 === null) {
            // client has not specified p3 (comparison)
            // which means Criteria::EQUAL but has also specified p2 == null
            // which is a valid combination we should handle by creating "IS NULL"
            this.addAnd(p1, p2, Criteria.EQUAL);
        }
        return this;
    };

    /**
     * This method adds a new criterion to the list of criterias.
     * If a criterion for the requested column already exists, it is
     * "OR"ed to the existing criterion.
     *
     * Any comparison can be used.
     *
     * Supports a number of different signatures:
     *
     * addOr(column, value, comparison)
     * <code>
     * crit = orig_crit.addOr(&quot;column&quot;,
     *                                      &quot;value&quot;
     *                                      &quot;Criterion::GREATER_THAN&quot;);
     * </code>
     *
     * addOr(column, value)
     * <code>
     * crit = orig_crit.addOr(&quot;column&quot;, &quot;value&quot;);
     * </code>
     *
     * addOr(Criterion)
     *
     * @return Criteria A modified Criteria object.
     */
    this.addOr = function addOr(p1, p2, p3)
    {
        if (p3 !== null) {
            // addOr(column, value, comparison)
            oc = this.getCriterion(p1);
            nc = new Criterion(this, p1, p2, p3);
            if (oc === null) {
                this.map[p1] = nc;
            } else {
                oc.addOr(nc);
            }
        } else if (p2 !== null) {
            // addOr(column, value)
            this.addOr(p1, p2, Criteria.EQUAL);
        } else if (p1 instanceof Criterion) {
            // addOr(Criterion)
            c = p1;
            oc = this.getCriterion(c.getTable() + '.' + c.getColumn());
            if (oc === null) {
                this.add(c);
            } else {
                oc.addOr(c);
            }
        } else if (p2 === null && p3 === null) {
            // client has not specified p3 (comparison)
            // which means Criteria::EQUAL but has also specified p2 == null
            // which is a valid combination we should handle by creating "IS NULL"
            this.addOr(p1, p2, Criteria.EQUAL);
        }

        return this;
    };
}


// --------------------------------------------------------------------
// Criterion "inner" class
// --------------------------------------------------------------------

/**
 * This is an "inner" class that describes an object in the criteria.
 *
 * In Torque this is an inner class of the Criteria class.
 *
 * @author Hans Lellelid <hans@xmpl.org> (Propel)
 * @package propel.util
 */
function Criterion() {

    Criterion.UND = " AND ";
    Criterion.ODER = " OR ";

    /** Value of the CO. */
    var value;

    /** Comparison value.
     * @var SqlEnum
     */
    var comparison;

    /** Table name. */
    var table;

    /** Real table name */
    var realtable;

    /** Column name. */
    var column;

    /** flag to ignore case in comparision */
    var ignoreStringCase = false;

    /**
     * The DBAdaptor which might be used to get db specific
     * variations of sql.
     */
    var db;

    /**
     * other connected criteria and their conjunctions.
     */
    var clauses = [];
    var conjunctions = [];

    /** "Parent" Criteria class */
    var parent;

    /**
     * Create a new instance.
     *
     * @param Criteria parent The outer class (this is an "inner" class).
     * @param string column TABLE.COLUMN format.
     * @param mixed value
     * @param string comparison
     */
    this.__construct = function __construct(/* Criteria */outer, column, value, comparison)
    {
        list(this.table, this.column) = explode('.', column);
        this.value = value;
        this.comparison = (comparison === null ? Criteria::EQUAL : comparison);
        this.init(outer);
    };

    /**
    * Init some properties with the help of outer class
    * @param Criteria criteria The outer class
    */
    this.init = function init(criteria)
    {
        //init this.db
        try {
            db = Propel::getDB(criteria.getDbName());
            this.setDB(db);
        } catch (e) {
            // we are only doing this to allow easier debugging, so
            // no need to throw up the exception, just make note of it.
            Propel::log("Could not get a DBAdapter, so sql may be wrong", Propel::LOG_ERR);
        }

        //init this.realtable
        realtable = criteria.getTableForAlias(this.table);
        if(!realtable) realtable = this.table;
        this.realtable = realtable;

    };

    /**
     * Get the column name.
     *
     * @return string A String with the column name.
     */
    this.getColumn = function getColumn()
    {
        return this.column;
    };

    /**
     * Set the table name.
     *
     * @param name A String with the table name.
     * @return void
     */
    this.setTable = function setTable(name)
    {
        this.table = name;
    };

    /**
     * Get the table name.
     *
     * @return string A String with the table name.
     */
    this.getTable = function getTable()
    {
        return this.table;
    };

    /**
     * Get the comparison.
     *
     * @return string A String with the comparison.
     */
    this.getComparison = function getComparison()
    {
        return this.comparison;
    };

    /**
     * Get the value.
     *
     * @return mixed An Object with the value.
     */
    this.getValue = function getValue()
    {
        return this.value;
    };

    /**
     * Get the value of db.
     * The DBAdapter which might be used to get db specific
     * variations of sql.
     * @return DBAdapter value of db.
     */
    this.getDB = function getDB()
    {
        return this.db;
    };

    /**
     * Set the value of db.
     * The DBAdapter might be used to get db specific variations of sql.
     * @param DBAdapter v Value to assign to db.
     * @return void
     */
    this.setDB = function setDB(/*DBAdapter*/ v)
    {
        this.db = v;
        for(i=0, _i=count(this.clauses); i < _i; i++) {
            this.clauses[i].setDB(v);
        }
    };

    /**
     * Sets ignore case.
     *
     * @param boolean b True if case should be ignored.
     * @return Criterion A modified Criterion object.
     */
    this.setIgnoreCase = function setIgnoreCase(b)
    {
        this.ignoreStringCase = b;
        return this;
    };

    /**
     * Is ignore case on or off?
     *
     * @return boolean True if case is ignored.
     */
     this.isIgnoreCase = function isIgnoreCase()
     {
         return this.ignoreStringCase;
     };

    /**
     * Get the list of clauses in this Criterion.
     * @return array
     */
    function getClauses()
    {
        return this.clauses;
    };

    /**
     * Get the list of conjunctions in this Criterion
     * @return array
     */
    function getConjunctions()
    {
        return this.conjunctions;
    };

    /**
     * Append an AND Criterion onto this Criterion's list.
     */
    this.addAnd = function addAnd(criterion)
    {
        this.clauses.push(criterion);
        this.conjunctions.push(Criterion.UND);
        return this;
    };

    /**
     * Append an OR Criterion onto this Criterion's list.
     * @return Criterion
     */
    this.addOr = function addOr(/*Criterion*/ criterion)
    {
        this.clauses.push(criterion);
        this.conjunctions.push(Criteria.ODER);
        return this;
    };

    /**
     * Appends a Prepared Statement representation of the Criterion
     * onto the buffer.
     *
     * @param string &sb The stringbuffer that will receive the Prepared Statement
     * @param array params A list to which Prepared Statement parameters
     * will be appended
     * @return void
     * @throws PropelException - if the expression builder cannot figure out how to turn a specified
     *                           expression into proper SQL.
     */
    this.appendPsTo = function appendPsTo(sb, params)
    {
        if (this.column === null) {
            return;
        }

        db = this.getDb();
        clausesLength = count(this.clauses);
        for(j = 0; j < clausesLength; j++) {
            sb += '(';
        };

        if (Criteria::CUSTOM === this.comparison) {
            if (this.value !== "") {
                sb += toString(this.value);
            }
        } else {

            if  (this.table === null) {
                field = this.column;
            } else {
                field = this.table + '.' + this.column;
            }

            // Check to see if table is an alias & store real name, if so
            // (real table name is needed for the returned params array)
            realtable = this.realtable;

            // There are several different types of expressions that need individual handling:
            // IN/NOT IN, LIKE/NOT LIKE, and traditional expressions.

            // OPTION 1:  table.column IN (?, ?) or table.column NOT IN (?, ?)
            if (this.comparison === Criteria::IN || this.comparison === Criteria::NOT_IN) {

				values = $A(this.value);
				valuesLength = count(values);
				if (valuesLength == 0) {
				    // a SQL error will result if we have COLUMN IN (), so replace it with an expression
				    // that will always evaluate to FALSE for Criteria::IN and TRUE for Criteria::NOT_IN
					sb += (this.comparison === Criteria::IN) ? "1<>1" : "1=1";
				} else {
					sb += field + this.comparison;
	                for (i=0; i < valuesLength; i++) {
	                    params.push(array('table' => realtable, 'column' => this.column, 'value' => values[i]));
	                }
	                inString = '(' + substr(str_repeat("?,", valuesLength), 0, -1) + ')';
	                sb += inString;
				};

            // OPTION 2:  table.column LIKE ? or table.column NOT LIKE ?  (or ILIKE for Postgres)
            } else if (this.comparison === Criteria::LIKE || this.comparison === Criteria::NOT_LIKE
                || this.comparison === Criteria::ILIKE || this.comparison === Criteria::NOT_ILIKE) {
                // Handle LIKE, NOT LIKE (and related ILIKE, NOT ILIKE for Postgres)

                // If selection is case insensitive use ILIKE for PostgreSQL or SQL
                // UPPER() function on column name for other databases.
                if (this.ignoreStringCase) {
                    include_once 'propel/adapter/DBPostgres.php'; // for instanceof, since is_a() is not E_STRICT
                    if (db instanceof DBPostgres) { // use is_a() because instanceof needs class to have been loaded
                        if (this.comparison === Criteria::LIKE) {
                            this.comparison = Criteria::ILIKE;
                        } else if (this.comparison === Criteria::NOT_LIKE) {
                            this.comparison = Criteria::NOT_ILIKE;
                          };
                    } else {
                        field = db.ignoreCase(field);
                    };
                };

                sb += field . this.comparison;

                // If selection is case insensitive use SQL UPPER() function
                // on criteria or, if Postgres we are using ILIKE, so not necessary.
                if (this.ignoreStringCase && !(db instanceof DBPostgres)) {
                    sb += db.ignoreCase('?');
                } else {
                    sb += '?';
                };

                params[] = array('table' => realtable, 'column' => this.column, 'value' => this.value);

            // OPTION 3:  table.column = ? or table.column >= ? etc. (traditional expressions, the default)
            } else {

                // NULL VALUES need special treatment because the SQL syntax is different
                // i.e. table.column IS NULL rather than table.column = null
                if (this.value !== null) {

                    // ANSI SQL functions get inserted right into SQL (not escaped, etc.)
                    if (this.value === Criteria::CURRENT_DATE || this.value === Criteria::CURRENT_TIME || this.value === Criteria::CURRENT_TIMESTAMP) {
                        sb += field + this.comparison + this.value;
                    } else {
                        // default case, it is a normal col = value expression; value
                        // will be replaced w/ '?' and will be inserted later using native Creole functions
                        if (this.ignoreStringCase) {
                            sb += db.ignoreCase(field) + this.comparison + db.ignoreCase("?");
                        } else {
                            sb += field + this.comparison + "?";
                        }
                        // need to track the field in params, because
                        // we'll need it to determine the correct setter
                        // method later on (e.g. field 'review.DATE' => setDate());
                        params.push(array('table' => realtable, 'column' => this.column, 'value' => this.value));
                    }
                } else {

                    // value is null, which means it was either not specified or specifically
                    // set to null.
                    if (this.comparison === Criteria::EQUAL || this.comparison === Criteria::ISNULL) {
                        sb += field . Criteria::ISNULL;
                    } else if (this.comparison === Criteria::NOT_EQUAL || this.comparison === Criteria::ISNOTNULL) {
                        sb += field . Criteria::ISNOTNULL;
                    } else {
                        // for now throw an exception, because not sure how to interpret this
                        throw new PropelException("Could not build SQL for expression: field " . this.comparison . " NULL");
                    }

                }

            }
        };

        for(i=0; i < clausesLength; i++) {
            sb += this.conjunctions[i];
            this.clauses[i].appendPsTo(sb, params);
            sb += ')';
        };
    };

    /**
     * This method checks another Criteria to see if they contain
     * the same attributes and hashtable entries.
     * @return boolean
     */
    this.equals = function(obj)
    {
        if (this === obj) {
            return true;
        };

        if ((obj === null) || !(obj instanceof Criterion)) {
            return false;
        };

        crit = obj;

        isEquiv = ( ( (this.table === null && crit.getTable() === null)
            || ( this.table !== null && this.table === crit.getTable() )
                          )
            && this.column === crit.getColumn()
            && this.comparison === crit.getComparison());

        // check chained criterion

        clausesLength = count(this.clauses);
        isEquiv &= (count(crit.getClauses()) == clausesLength);
        critConjunctions = crit.getConjunctions();
        critClauses = crit.getClauses();
        for (i=0; i < clausesLength && isEquiv; i++) {
            isEquiv &= (this.conjunctions[i] === critConjunctions[i]);
            isEquiv &= (this.clauses[i] === critClauses[i]);
        };

		if (isEquiv) {
		    isEquiv &= this.value === crit.getValue();
		};

        return isEquiv;
    };

    /**
     * Returns a hash code value for the object.
     */
    this.hashCode = function hashCode()
    {
        h = crc32(serialize(this.value)) ^ crc32(this.comparison);

        if (this.table !== null) {
            h ^= crc32(this.table);
        };

        if (this.column !== null) {
            h ^= crc32(this.column);
        };

        clausesLength = count(this.clauses);
        for(i=0; i < clausesLength; i++) {
            this.clauses[i].appendPsTo(sb="", params=[]);
            h ^= crc32(serialize(array(sb, params)));
        };

        return h;
    };

    /**
     * Get all tables from nested criterion objects
     * @return array
     */
    this.getAllTables = function getAllTables()
    {
        tables = [];
        this.addCriterionTable(this, tables);
        return tables;
    };

    /**
     * method supporting recursion through all criterions to give
     * us a string array of tables from each criterion
     * @return void
     */
    private function addCriterionTable(Criterion c, &s)
    {
        s[] = c.getTable();
        clauses = c.getClauses();
        clausesLength = count(clauses);
        for(i = 0; i < clausesLength; i++) {
            this.addCriterionTable(clauses[i], s);
        };
    };

    /**
     * get an array of all criterion attached to this
     * recursing through all sub criterion
     * @return array Criterion[]
     */
    this.getAttachedCriterion = function getAttachedCriterion()
    {
        crits = [];
        this.traverseCriterion(this, crits);
        return crits;
    };

    /**
     * method supporting recursion through all criterions to give
     * us an array of them
     * @param Criterion c
     * @param array &a
     * @return void
     */
    private function traverseCriterion(Criterion c, &a)
    {
        a[] = c;
        clauses = c.getClauses();
        clausesLength = count(clauses);
        for(i=0; i < clausesLength; i++) {
            this.traverseCriterion(clauses[i], a);
        };
    };
};


    /** Comparison type. */
    Criteria.EQUAL = "=";

    /** Comparison type. */
    Criteria.NOT_EQUAL = "<>";

    /** Comparison type. */
    Criteria.ALT_NOT_EQUAL = "!=";

    /** Comparison type. */
    Criteria.GREATER_THAN = ">";

    /** Comparison type. */
    Criteria.LESS_THAN = "<";

    /** Comparison type. */
    Criteria.GREATER_EQUAL = ">=";

    /** Comparison type. */
    Criteria.LESS_EQUAL = "<=";

    /** Binary math operator: AND */
    Criteria.BINARY_AND = "&";

    /** Binary math operator: OR */
    Criteria.BINARY_OR = "|";

    /** "IS NULL" null comparison */
    Criteria.ISNULL = " IS NULL ";

    /** "IS NOT NULL" null comparison */
    Criteria.ISNOTNULL = " IS NOT NULL ";