describe('Housing Document Upload', () => {
    beforeEach(() => {
        cy.refreshDatabase()
        cy.visit('/') // Adjust the route if necessary
    })

    it('shows message when no data is available', () => {
        cy.contains('There is no data to calculate. Upload a document first.').should('exist')
    })

    it('uploads a non-csv document', () => {
        cy.get('input[type="file"]').selectFile('tests/cypress/fixtures/non-csv.txt')
        cy.get('#confirmation').click()
        cy.get('button[type="submit"]').click()
        cy.contains('Error! The data store was not successful due to the following errors:')
    })

    it('uploads a csv document with duplicated records', () => {
        cy.get('input[type="file"]').selectFile(
            'tests/cypress/fixtures/valid-csv-with-duplicates.csv',
        )
        cy.get('#confirmation').click()
        cy.get('button[type="submit"]').click()
        cy.contains('Loading...').should('exist')
        cy.contains('Error! The data store was not successful due to the following errors:')
        cy.contains('Duplicated records:')
        cy.contains('1995-04-01 - city of london')
        cy.contains('2019-12-01 - england')
    })

    it('uploads a csv document with missing date', () => {
        cy.get('input[type="file"]').selectFile(
            'tests/cypress/fixtures/valid-csv-with-bad-date.csv',
        )
        cy.get('#confirmation').click()
        cy.get('button[type="submit"]').click()
        cy.contains('Loading...').should('exist')
        cy.contains('Error! The data store was not successful due to the following errors:')
    })

    it('uploads a csv document with missing area', () => {
        cy.get('input[type="file"]').selectFile(
            'tests/cypress/fixtures/valid-csv-with-missing-area.csv',
        )
        cy.get('#confirmation').click()
        cy.get('button[type="submit"]').click()
        cy.contains('Loading...').should('exist')
        cy.contains('Error! The data store was not successful due to the following errors:')
        cy.contains('The area field is required.')
    })

    it('uploads a CSV file successfully and checks the stats', () => {
        cy.get('input[type="file"]').selectFile('tests/cypress/fixtures/valid-csv.csv')
        cy.get('#confirmation').click()
        cy.get('button[type="submit"]').click()

        cy.contains('Loading...').should('exist')
        cy.contains('Success!').should('exist')
        cy.contains('Data successfully saved into the database.').should('exist')

        cy.contains('Avg price: 195,220.792').should('exist')
        cy.contains('Total houses sold: 942741').should('exist')
        cy.contains('No of crimes in 2011: 0').should('exist')
        cy.contains('Avg price by year in London area').should('exist')
        cy.contains('1995: 91,455.125').should('exist')
    })

    it('tries to submit without checkbox confirmation', () => {
        cy.get('input[type="file"]').selectFile('tests/cypress/fixtures/valid-csv.csv')
        cy.get('button[type="submit"]').click()
        cy.contains('Loading...').should('not.exist')
    })

    it('tries to submit without a file', () => {
        cy.get('#confirmation').click()
        cy.get('button[type="submit"]').click()
        cy.contains('Loading...').should('not.exist')
    })
})
